<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Filters
{
    /** Gesammelte Copyright-Einträge (pro Request). */
    private static array $copyrightEntries = [];
    /** Cache für den gewählten Filter-Tag. */
    private static ?string $copyrightFilterTag = null;


    public static function register(): void {
        $tag = self::getCopyrightFilterTag();
        if ($tag) {
            // Theme ruft z.B. apply_filters('fau_copyright_info', [], $args) auf
            add_filter($tag, [__CLASS__, 'collectCopyrightInfo'], 10, 2);
        }
    }

    /**
     * Von überall im Plugin aufrufen, um einen Eintrag hinzuzufügen.
     * @param string $text      Copyright-Text (wird getrimmt/gestrippt)
     * @param int    $image_id  Attachment-ID (0, wenn unbekannt)
     */
    public static function pushCopyright(string $text, int $image_id = 0): void {
        $text = trim(wp_strip_all_tags($text));
        if ($text === '') {
            return;
        }
        self::$copyrightEntries[] = [
            'text'     => $text,
            'image_id' => max(0, (int) $image_id),
        ];
    }

    /**
     * Callback für den Theme-Filter:
     * Merged unsere gesammelten Einträge in die vom Theme übergebenen $entries.
     *
     * @param mixed $entries Array (oder gemischt), das vom Theme übergeben wird
     * @param mixed $args    optionale Zusatzargumente
     * @return array         Normalisiertes Array von ['text' => string, 'image_id' => int]
     */
    public static function collectCopyrightInfo($entries, $args = []): array {
        $entriesArr = is_array($entries) ? $entries : [$entries];

        // Unsere Einträge anhängen
        if (!empty(self::$copyrightEntries)) {
            $entriesArr = array_merge($entriesArr, self::$copyrightEntries);
        }

        // Normalisieren: alles auf ['text' => string, 'image_id' => int] bringen
        $normalized = [];
        foreach ($entriesArr as $e) {
            if (is_array($e)) {
                $text   = isset($e['text']) ? trim((string) $e['text']) : '';
                $imgId  = isset($e['image_id']) ? (int) $e['image_id'] : 0;
                if ($text !== '') {
                    $normalized[] = ['text' => $text, 'image_id' => max(0, $imgId)];
                }
            } elseif (is_scalar($e)) {
                $text = trim((string) $e);
                if ($text !== '') {
                    $normalized[] = ['text' => $text, 'image_id' => 0];
                }
            }
        }

        // Deduplizieren nach text+image_id (case-insensitive für Text)
        $seen = [];
        $out  = [];
        foreach ($normalized as $item) {
            $key = Utils::lower($item['text']) . '|' . (string) $item['image_id'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * Ermittelt den passenden Filter-Tag anhand des aktiven Themes.
     * - FAU-Elemental → 'fau_elemental_copyright_info'
     * - andere FAU-* Themes → 'fau_copyright_info'
     * - sonst → null (wir registrieren dann keinen Filter)
     */
    private static function getCopyrightFilterTag(): ?string  {
        if (self::$copyrightFilterTag !== null) {
            return self::$copyrightFilterTag;
        }

        $theme      = function_exists('wp_get_theme') ? wp_get_theme() : null;
        $name       = $theme ? (string) $theme->get('Name') : '';
        $stylesheet = (string) get_option('stylesheet');
        $template   = (string) get_option('template');

        // Elemental prüfen
        $isElemental =
            stripos($name, 'FAU-Elemental') !== false
            || $stylesheet === 'fau-elemental'
            || $template === 'fau-elemental';

        if ($isElemental) {
            self::$copyrightFilterTag = 'fau_elemental_copyright_info';
            return self::$copyrightFilterTag;
        }

        // Andere FAU-Themes (Name oder Stylesheet/Template beginnt mit 'fau'/'FAU')
        $isOtherFAU =
            preg_match('/^fau[\s-]?/i', $stylesheet)
            || preg_match('/^fau[\s-]?/i', $template)
            || preg_match('/^fau/i', $name);

        if ($isOtherFAU) {
            self::$copyrightFilterTag = 'fau_copyright_info';
            return self::$copyrightFilterTag;
        }

        // Kein FAU-Theme → keinen Filter registrieren
        self::$copyrightFilterTag = null;
        return null;
    }

    /**
     * Optional: für Debug/Tests – gibt den aktuell aktiven Copyright-Filter-Tag zurück.
     */
    public static function getActiveCopyrightFilterTag(): ?string {
        return self::getCopyrightFilterTag();
    }

}