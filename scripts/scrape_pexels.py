#!/usr/bin/env python3
"""Scrape Pexels search result pages and collect candidate image URLs."""
import os
import re
import sys
import urllib.parse
import urllib.request

UA = ("Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 "
      "(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36")
CAND = "/tmp/opencode/wpyoga/candidates"
os.makedirs(CAND, exist_ok=True)

QUERIES = {
    "slider": "yoga meditation woman",
    "hero": "yoga pose silhouette sunset",
    "studio": "yoga class studio",
    "service": "yoga pose woman studio",
    "food": "healthy food bowl",
    "spa": "spa massage relaxation",
    "fitness": "running fitness woman gym",
    "breath": "yoga breathing meditation",
    "mat": "yoga mat blocks props",
}


def scrape(query, want=24):
    import subprocess
    import time
    url = "https://www.pexels.com/search/" + urllib.parse.quote(query, safe="") + "/"
    html = ""
    for attempt in range(5):
        p = subprocess.run(
            ["curl", "-sL", "--max-time", "45", "-A", UA,
             "-H", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
             "-H", "Accept-Language: en-US,en;q=0.9",
             "-b", "/tmp/opencode/wpyoga/pex.cookies", "-c", "/tmp/opencode/wpyoga/pex.cookies",
             url], capture_output=True, text=True)
        html = p.stdout
        if len(html) > 20000:
            break
        time.sleep(8 + attempt * 6)
    else:
        raise RuntimeError(f"blocked for '{query}'")
    time.sleep(3)
    ids = re.findall(
        r"images\.pexels\.com/photos/(\d+)/pexels-photo-\1\.(?:jpeg|jpg)", html)
    seen, out = set(), []
    for i in ids:
        if i in seen:
            continue
        seen.add(i)
        out.append(f"https://images.pexels.com/photos/{i}/pexels-photo-{i}.jpeg"
                   f"?auto=compress&cs=tinysrgb&w=1600")
        if len(out) >= want:
            break
    return out


def main():
    manifest = {}
    for name, q in QUERIES.items():
        try:
            urls = scrape(q)
        except Exception as e:
            print(f"FAIL scrape {name}: {e}")
            continue
        manifest[name] = urls
        print(f"{name:10s} {len(urls):3d} candidates  <- '{q}'")
    with open(os.path.join(CAND, "manifest.txt"), "w") as f:
        for name, urls in manifest.items():
            f.write(name + "\t" + "\n".join(urls) + "\n")


if __name__ == "__main__":
    main()
