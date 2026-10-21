#!/usr/bin/env python3
"""Fetch CC-licensed yoga/fitness photos from Wikimedia Commons for demo content."""
import json
import os
import time
import urllib.parse
import urllib.request

OUT = "/tmp/opencode/wpyoga/demo-images"
os.makedirs(OUT, exist_ok=True)

UA = "OpenCode-Demo-Fetcher/1.0 (local wordpress theme preview; contact: local)"

TARGETS = [
    ("slider-1", ["yoga asana pose", "woman yoga outdoors", "Virabhadrasana yoga", "Trikonasana"]),
    ("slider-2", ["meditation lotus position", "Padmasana", "yoga meditation"]),
    ("slider-3", ["yoga class group", "yoga practice group", "yoga lesson"]),
    ("service-1", ["yoga mat", "yoga studio", "yoga props mat blocks"]),
    ("service-2", ["balance pose yoga", "Vrikshasana tree pose", "yoga balance"]),
    ("service-3", ["Surya namaskar", "sun salutation yoga", "yoga flow sequence"]),
    ("post-1", ["yoga stretching exercise", "yoga forward bend", "yoga asana woman"]),
    ("post-2", ["healthy food fruit", "vegetables healthy diet", "fruit bowl"]),
    ("post-3", ["running fitness cardio", "treadmill running", "fitness workout"]),
    ("post-4", ["spa massage", "hot stone massage", "relaxation spa"]),
    ("post-5", ["sunrise wellness", "morning coffee healthy", "fresh juice smoothie"]),
    ("post-6", ["yoga breathing", "pranayama", "yoga meditation sitting"]),
    ("callout", ["yoga hall", "yoga group class mat", "yoga asana practice"]),
]


def search(term, limit=30):
    q = {
        "action": "query",
        "format": "json",
        "generator": "search",
        "gsrsearch": f"filetype:bitmap {term}",
        "gsrnamespace": "6",
        "gsrlimit": str(limit),
        "prop": "imageinfo",
        "iiprop": "url|size|mime",
        "iiurlwidth": "1600",
    }
    url = "https://commons.wikimedia.org/w/api.php?" + urllib.parse.urlencode(q)
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    for attempt in range(4):
        try:
            with urllib.request.urlopen(req, timeout=40) as r:
                data = json.load(r)
            break
        except Exception as e:
            if attempt == 3:
                raise
            time.sleep(4 * (attempt + 1))
    pages = data.get("query", {}).get("pages", {})
    out = []
    for p in pages.values():
        ii = (p.get("imageinfo") or [None])[0]
        if not ii or ii.get("mime") not in ("image/jpeg", "image/png"):
            continue
        w, h = ii.get("width", 0), ii.get("height", 0)
        if w < 900 or h < 500 or w / max(h, 1) < 0.9:
            continue
        out.append({"title": p["title"], "thumb": ii.get("thumburl"), "url": ii.get("url")})
    return out


def download(url, path):
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=60) as r:
        data = r.read()
    if len(data) < 25000:
        return False
    with open(path, "wb") as f:
        f.write(data)
    return True


def main():
    used = set()
    for name, terms in TARGETS:
        path = os.path.join(OUT, name + ".jpg")
        if os.path.exists(path) and os.path.getsize(path) > 25000:
            print(f"skip {name} (exists)")
            continue
        got = False
        for term in terms:
            try:
                results = search(term)
            except Exception as e:
                print(f"  search error '{term}': {e}")
                continue
            time.sleep(1.5)
            for r in results:
                if r["title"] in used:
                    continue
                for candidate in (r["thumb"], r["url"]):
                    if not candidate:
                        continue
                    try:
                        if download(candidate, path):
                            used.add(r["title"])
                            print(f"OK   {name:10s} <- [{term}] {r['title'][:55]}")
                            got = True
                            break
                    except Exception:
                        continue
                if got:
                    break
            if got:
                break
            time.sleep(1.5)
        if not got:
            print(f"FAIL {name}")


if __name__ == "__main__":
    main()
