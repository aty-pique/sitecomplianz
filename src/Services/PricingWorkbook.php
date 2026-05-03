<?php

declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RuntimeException;

/**
 * Charge la grille tarifaire XLSX une fois par requête (cache statique simple).
 */
final class PricingWorkbook
{
    private static ?Spreadsheet $spreadsheet = null;

    private static function path(): string
    {
        return ROOT_PATH . '/public/assets/docs/Grille_Tarifaire_FINAL_v2.xlsx';
    }

    public static function isAvailable(): bool
    {
        return is_readable(self::path());
    }

    public static function get(): Spreadsheet
    {
        if (self::$spreadsheet !== null) {
            return self::$spreadsheet;
        }
        if (!self::isAvailable()) {
            throw new RuntimeException('Fichier grille tarifaire introuvable ou illisible.');
        }
        if (!extension_loaded('zip')) {
            throw new RuntimeException(
                'L’extension PHP zip est requise pour lire le fichier Excel. Activez extension=zip dans php.ini.'
            );
        }
        self::$spreadsheet = IOFactory::load(self::path());

        return self::$spreadsheet;
    }

    public static function reset(): void
    {
        self::$spreadsheet = null;
    }

    /**
     * @return float|int|string|null
     */
    public static function calculatedValue(string $sheetName, string $coordinate): mixed
    {
        $wb = self::get();
        $sheet = $wb->getSheetByName($sheetName);
        if ($sheet === null) {
            throw new RuntimeException('Feuille Excel inconnue : ' . $sheetName);
        }

        return $sheet->getCell($coordinate)->getCalculatedValue();
    }
}
