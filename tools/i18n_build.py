#!/usr/bin/env python3
"""i18n build tool for Hooshyar Commerce Kit.

Extracts translatable strings from PHP sources, merges with the Persian
translations stored in languages/hooshyar-commerce-kit-fa_IR.po and writes
.po / .mo files (msgfmt is not required).

Usage: python3 tools/i18n_build.py
"""
import os, re, struct, array, sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DOMAIN = 'hooshyar-commerce-kit'
PO_PATH = os.path.join(ROOT, 'languages', DOMAIN + '-fa_IR.po')
POT_PATH = os.path.join(ROOT, 'languages', DOMAIN + '.pot')
MO_PATH = os.path.join(ROOT, 'languages', DOMAIN + '-fa_IR.mo')

FUNCS = r"(?:__|_e|esc_html__|esc_attr__|esc_html_e|esc_attr_e|esc_html_x|esc_attr_x)"
RE = re.compile(FUNCS + r"\(\s*(['\"])((?:(?!\1).|\\.)*)\1\s*,\s*['\"]" + re.escape(DOMAIN) + r"['\"]", re.S)

SKIP_DIRS = {'.git', 'dist', 'node_modules', 'languages', 'tools', 'preview'}


def php_files():
    out = []
    for dirpath, dirnames, filenames in os.walk(ROOT):
        dirnames[:] = [d for d in dirnames if d not in SKIP_DIRS]
        for f in filenames:
            if f.endswith('.php'):
                out.append(os.path.join(dirpath, f))
    return sorted(out)


def unescape(s):
    return s.replace("\\'", "'").replace('\\"', '"').replace('\\\\', '\\').replace('\\n', '\n')


def extract():
    strings = set()
    for path in php_files():
        src = open(path, encoding='utf-8').read()
        for m in RE.finditer(src):
            strings.add(unescape(m.group(2)))
    strings.discard('')
    return sorted(strings)


def load_po(path):
    """Parse a simple po file into {msgid: msgstr}."""
    if not os.path.isfile(path):
        return {}
    text = open(path, encoding='utf-8').read()
    entries = {}
    blocks = re.split(r'\n\s*\n', text)
    for block in blocks:
        ids = re.findall(r'^msgid\s+((?:"(?:[^"\\]|\\.)*"\s*)+)', block, re.M)
        strs = re.findall(r'^msgstr\s+((?:"(?:[^"\\]|\\.)*"\s*)+)', block, re.M)
        if not ids or not strs:
            continue

        def join(block_part):
            parts = re.findall(r'"((?:[^"\\]|\\.)*)"', block_part)
            return unescape(''.join(parts))

        msgid = join(ids[0])
        msgstr = join(strs[0])
        if msgid == '':
            continue
        entries[msgid] = msgstr
    return entries


def po_escape(s):
    return s.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n')


def write_po(path, strings, translations, header):
    lines = [header.strip(), '']
    for s in strings:
        lines.append('#. generated')
        lines.append('msgid "%s"' % po_escape(s))
        lines.append('msgstr "%s"' % po_escape(translations.get(s, '')))
        lines.append('')
    open(path, 'w', encoding='utf-8').write('\n'.join(lines))


def write_mo(path, translations):
    # struct-packed .mo (same format as GNU msgfmt)
    keys = sorted(translations.keys())
    ids = b'\x00'.join(k.encode('utf-8') for k in keys)
    strs = b'\x00'.join(translations[k].encode('utf-8') for k in keys)
    keystart = 7 * 4 + 16 * len(keys)
    valuestart = keystart + len(ids)
    koffsets = []
    voffsets = []
    o1 = o2 = 0
    for k in keys:
        koffsets += [len(k.encode('utf-8')), o1]
        o1 += len(k.encode('utf-8')) + 1
        t = translations[k]
        voffsets += [len(t.encode('utf-8')), o2]
        o2 += len(t.encode('utf-8')) + 1
    output = struct.pack('Iiiiiii', 0x950412de, 0, len(keys), 7 * 4, 7 * 4 + len(keys) * 8, 0, 0)
    output += array.array('i', koffsets).tobytes()
    output += array.array('i', voffsets).tobytes()
    output += ids + b'\x00' + strs + b'\x00'
    with open(path, 'wb') as f:
        f.write(output)


def main():
    strings = extract()
    translations = load_po(PO_PATH)

    po_header = (
        'msgid ""\nmsgstr ""\n'
        '"Project-Id-Version: ' + DOMAIN + '\\n"\n'
        '"Language: fa_IR\\n"\n'
        '"Content-Type: text/plain; charset=UTF-8\\n"\n'
        '"Content-Transfer-Encoding: 8bit\\n"\n'
        '"Plural-Forms: nplurals=2; plural=(n > 1);\\n"'
    )

    write_po(PO_PATH, strings, translations, po_header)
    write_po(POT_PATH, strings, {}, po_header)

    complete = {s: translations[s] for s in strings if translations.get(s)}
    write_mo(MO_PATH, complete)

    missing = [s for s in strings if not translations.get(s)]
    print('total strings:', len(strings))
    print('translated:', len(complete))
    print('missing:', len(missing))
    for s in missing:
        print('  -', s[:100])


if __name__ == '__main__':
    main()
