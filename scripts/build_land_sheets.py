#!/usr/bin/env python3
"""Build contact sheets of landscape-orientation candidates only."""
import os
import subprocess

BASE = "/tmp/opencode/wpyoga/candidates"
DL = os.path.join(BASE, "img")
PHP = "/tmp/opencode/wpyoga/php"


def landscape(path):
    r = subprocess.run([PHP, "-r",
                        '$s=getimagesize($argv[1]); if(!$s){exit(1);} echo ($s[0]/$s[1] > 1.25 && $s[0] >= 1200) ? "L" : "P";',
                        path], capture_output=True, text=True)
    return r.stdout.strip() == "L"


def main():
    groups = {}
    for f in sorted(os.listdir(DL)):
        if not f.endswith(".jpg"):
            continue
        cat = f.rsplit("_", 2)[0]
        groups.setdefault(cat, []).append(f)

    code = r'''<?php
$list = explode("\n", trim($argv[1]));
$cols = 5; $tw = 340; $th = 230;
$rows = (int)ceil(count($list)/$cols);
$out = imagecreatetruecolor($cols*$tw, $rows*$th);
imagefill($out, 0, 0, imagecolorallocate($out, 18, 18, 18));
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
    open("/tmp/opencode/wpyoga/sheet2.php", "w").write(code)

    all_land = []
    for cat, files in groups.items():
        land = [f for f in files if landscape(os.path.join(DL, f))]
        all_land += land
        if not land:
            continue
        lst = "\n".join(f"{os.path.join(DL, f)}|{f}" for f in land)
        subprocess.run([PHP, "/tmp/opencode/wpyoga/sheet2.php", lst,
                        f"{BASE}/land_{cat}.jpg"], check=True)
        print(f"land_{cat}.jpg ({len(land)})")

    lst = "\n".join(f"{os.path.join(DL, f)}|{f}" for f in all_land)
    subprocess.run([PHP, "/tmp/opencode/wpyoga/sheet2.php", lst,
                    f"{BASE}/land_ALL.jpg"], check=True)
    print(f"land_ALL.jpg ({len(all_land)})")


if __name__ == "__main__":
    main()
