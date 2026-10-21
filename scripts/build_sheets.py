#!/usr/bin/env python3
"""Download pexels candidates and build labelled contact sheets for review."""
import os
import re
import subprocess

BASE = "/tmp/opencode/wpyoga/candidates"
DL = os.path.join(BASE, "img")
os.makedirs(DL, exist_ok=True)
UA = ("Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 "
      "(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36")


def parse_manifest():
    txt = open(os.path.join(BASE, "manifest.txt")).read()
    groups = {}
    for block in re.split(r"\n(?=\w+\t)", txt):
        lines = [l for l in block.strip().split("\n") if l.strip()]
        if not lines:
            continue
        head = lines[0]
        name = head.split("\t")[0]
        urls = [l for l in lines[1:] if l.startswith("http")]
        groups[name] = urls
    return groups


def fetch(url, path):
    if os.path.exists(path) and os.path.getsize(path) > 15000:
        return True
    r = subprocess.run(["curl", "-sL", "--max-time", "40", "-A", UA, "-o", path, url])
    return r.returncode == 0 and os.path.exists(path) and os.path.getsize(path) > 15000


def main():
    groups = parse_manifest()
    for name, urls in groups.items():
        for i, u in enumerate(urls):
            pid = re.search(r"/photos/(\d+)/", u).group(1)
            fetch(u, os.path.join(DL, f"{name}_{i:02d}_{pid}.jpg"))
        print(f"downloaded {name}")
    # contact sheets
    php = "/tmp/opencode/wpyoga/php"
    for name in groups:
        files = sorted(f for f in os.listdir(DL) if f.startswith(name + "_"))
        if not files:
            continue
        lst = "\n".join(f"{os.path.join(DL, f)}|{f}" for f in files)
        code = r'''<?php
$list = explode("\n", trim($argv[1]));
$cols = 6; $tw = 300; $th = 200;
$rows = (int)ceil(count($list)/$cols);
$out = imagecreatetruecolor($cols*$tw, $rows*$th);
imagefill($out, 0, 0, imagecolorallocate($out, 18, 18, 18));
$white = imagecolorallocate($out, 255, 255, 255);
$yellow = imagecolorallocate($out, 255, 220, 80);
foreach ($list as $idx => $row) {
  $parts = explode("|", $row);
  $im = @imagecreatefromjpeg($parts[0]);
  if (!$im) { continue; }
  $w = imagesx($im); $h = imagesy($im);
  $r = max($w/$tw, $h/$th);
  $nw = (int)($w/$r); $nh = (int)($h/$r);
  $x = ($idx % $cols) * $tw + intdiv($tw-$nw, 2);
  $y = intdiv($idx, $cols) * $th + intdiv($th-$nh, 2);
  imagecopyresampled($out, $im, $x, $y, 0, 0, $nw, $nh, $w, $h);
  imagestring($out, 5, $x+4, $y+4, $parts[1], $yellow);
}
imagejpeg($out, $argv[2], 82);
'''
        open("/tmp/opencode/wpyoga/sheet.php", "w").write(code)
        subprocess.run([php, "/tmp/opencode/wpyoga/sheet.php", lst,
                        f"{BASE}/sheet_{name}.jpg"], check=True)
        print(f"sheet_{name}.jpg  ({len(files)} images)")


if __name__ == "__main__":
    main()
