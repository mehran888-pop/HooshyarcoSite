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

# Extra translations merged on top of the .po (used for new strings before the
# .po is regenerated). Key = English source string.
EXTRA_FA = {
    "Full width": "تمام‌عرض",
    "Boxed": "جعبه‌ای",
    "Boxed (inside container)": "جعبه‌ای (درون کادر)",
    "Header width": "عرض هدر",
    "Footer width": "عرض فوتر",
    "Site content width": "عرض محتوای سایت",
    "Header bar spans the browser width or floats inside the container.": "نوار هدر کل عرض مرورگر را بگیرد یا درون کادر وسط صفحه شناور باشد.",
    "Footer bar spans the browser width or sits inside the container.": "نوار فوتر کل عرض مرورگر را بگیرد یا درون کادر وسط صفحه قرار بگیرد.",
    "Content width: full (edge to edge) or boxed (container width).": "عرض محتوای سایت: تمام‌عرض (لبه‌به‌لبه) یا جعبه‌ای (محدود به کادر).",
    "Custom CSS": "CSS سفارشی",
    "Custom CSS printed on the storefront.": "کد CSS سفارشی که در فروشگاه چاپ می‌شود.",
    "Colors": "رنگ‌ها",
    "Shape & size": "شکل و اندازه",
    "Typography": "تایپوگرافی",
    "Custom code": "کد سفارشی",
    "Template & layout": "قالب و چیدمان",
    "Elements": "المان‌ها",
    "Shop page": "صفحه فروشگاه",
    "Product page": "صفحه محصول",
    "Header bar": "نوار هدر",
    "Menus & categories": "منوها و دسته‌بندی",
    "User & search": "کاربر و جستجو",
    "Footer": "فوتر",
    "Width & layout": "عرض و چیدمان",
    "Display scope": "حوزه نمایش",
    "Mobile nav": "منوی موبایل",
    "Effects": "افکت‌ها",
    "Sending": "ارسال",
    "Message": "پیام",
    "Environment & credentials": "محیط و احراز هویت",
    "Payment options": "گزینه‌های پرداخت",
    "Everything for your store design: templates, styles and integrations in one place.": "همه‌چیز برای طراحی فروشگاه شما: قالب‌ها، استایل‌ها و یکپارچه‌سازی‌ها در یک‌جا.",
    "Version": "نسخه",
    "Save settings": "ذخیره تنظیمات",
    "Settings": "تنظیمات",
    "Hooshyar Kit": "کیت هوشیار",
    "Hooshyar Commerce Kit — Settings": "کیت تجارت هوشیار — تنظیمات",
    "Notification Log": "گزارش اعلان‌ها",
}


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


def php_quote(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def write_dictionary(path, translations):
    lines = [
        '<?php',
        '/**',
        ' * Runtime Persian dictionary (generated by tools/i18n_build.py — do not edit).',
        ' * Guarantees a fully Persian UI regardless of the site locale or .mo loading.',
        ' *',
        ' * @package HooshyarCommerceKit',
        ' */',
        '',
        'defined( \'ABSPATH\' ) || exit;',
        '',
        '/**',
        ' * Class HCK_Lang_Fa',
        ' */',
        'class HCK_Lang_Fa {',
        '',
        '\t/**',
        '\t * English source => Persian translation.',
        '\t *',
        '\t * @var array',
        '\t */',
        '\tprivate static $map = array(',
    ]
    for k in sorted(translations.keys()):
        v = translations[k]
        if not v or v == k:
            continue
        lines.append('\t\t%s => %s,' % (php_quote(k), php_quote(v)))
    lines += [
        '\t);',
        '',
        '\t/**',
        '\t * Hook the gettext filter.',
        '\t */',
        '\tpublic static function init() {',
        '\t\tadd_filter( \'gettext\', array( __CLASS__, \'translate\' ), 10, 3 );',
        '\t}',
        '',
        '\t/**',
        '\t * Translate strings of this plugin to Persian.',
        '\t *',
        '\t * @param string $translation Translated text.',
        '\t * @param string $text        Source text.',
        '\t * @param string $domain      Text domain.',
        '\t * @return string',
        '\t */',
        '\tpublic static function translate( $translation, $text, $domain ) {',
        '\t\tif ( \'hooshyar-commerce-kit\' === $domain && isset( self::$map[ $text ] ) ) {',
        '\t\t\treturn self::$map[ $text ];',
        '\t\t}',
        '\t\treturn $translation;',
        '\t}',
        '}',
        '',
    ]
    open(path, 'w', encoding='utf-8').write('\n'.join(lines))


def main():
    strings = extract()
    translations = load_po(PO_PATH)
    translations.update(EXTRA_FA)
    strings = sorted(set(strings) | set(EXTRA_FA.keys()))

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
    write_dictionary(os.path.join(ROOT, 'includes', 'class-hck-lang-fa.php'), translations)

    missing = [s for s in strings if not translations.get(s)]
    print('total strings:', len(strings))
    print('translated:', len(complete))
    print('missing:', len(missing))
    for s in missing:
        print('  -', s[:100])


if __name__ == '__main__':
    main()
