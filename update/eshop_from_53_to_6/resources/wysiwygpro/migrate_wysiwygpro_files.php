<?php
/**
 * This file is part of OXID eSales developer documentation.
 *
 * OXID eSales developer documentation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales developer documentation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales OXID eShop Facts. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

/**
 * Configure the database connection to your OXID eShop 4.10 / 5.3 database
 */
$dbHost = "localhost";
$dbUser = "";
$dbPassword = "";
$dbName = "";

/**
 * These are the tables where files from the WysiwygPro editor get stored in OXID eShop 5.3.
 * If you either
 *  - use more than 4 languages in your OXID eShop
 *  - or use modules which have own database tables with content managed by WysiwygPro
 * please use the array $custom_tables.
 * Do not modify the array $core_tables.
 *
 */
$core_tables = [
    'oxactions' => [
        'OXLONGDESC',
        'OXLONGDESC_1',
        'OXLONGDESC_2',
        'OXLONGDESC_3'
    ],
    'oxlinks' => [
        'OXURLDESC',
        'OXURLDESC_1',
        'OXURLDESC_2',
        'OXURLDESC_3'
    ],
    'oxcategories' => [
        'OXLONGDESC',
        'OXLONGDESC_1',
        'OXLONGDESC_2',
        'OXLONGDESC_3'
    ],
    'oxcontents' => [
        'OXCONTENT',
        'OXCONTENT_1',
        'OXCONTENT_2',
        'OXCONTENT_3'
    ],
    'oxnews' => [
        'OXLONGDESC',
        'OXLONGDESC_1',
        'OXLONGDESC_2',
        'OXLONGDESC_3'
    ],
    'oxpayments' => [
        'OXLONGDESC',
        'OXLONGDESC_1',
        'OXLONGDESC_2',
        'OXLONGDESC_3'
    ],
    'oxartextends' => [
        'OXLONGDESC',
        'OXLONGDESC_1',
        'OXLONGDESC_2',
        'OXLONGDESC_3'
    ]
];

/**
 * If you either
 *  - use more than 4 languages in your OXID eShop
 *  - or use modules which have own database tables with content managed by WysiwygPro
 * uncomment and configure the array $custom_tables below.
 *
 * Please have a look at https://docs.oxid-esales.com/developer/en/6.0/system_architecture/language.html
 * on how to configure additional language specific OXID eShop core tables.
 *
 * Below there is an example for a fifth language configured in OXID eShop and one custom / module database table.
 */

/*
$custom_tables = [
    'my_custom_table' => [
        'LONGDESC'
    ],

    'oxactions' => [
        'OXLONGDESC_4'
    ],
    'oxlinks' => [
        'OXURLDESC_4'
    ],
    'oxcategories' => [
        'OXLONGDESC_4'
    ],
    'oxcontents' => [
        'OXCONTENT_4'
    ],
    'oxnews' => [
        'OXLONGDESC_4'
    ],
    'oxpayments' => [
        'OXLONGDESC_4'
    ],
    'oxartextends' => [
        'OXLONGDESC_4'
    ]
];
*/
if (isset($custom_tables) && is_array($custom_tables)) {
    $tables = array_merge_recursive($core_tables, $custom_tables);
} else {
    $tables = $core_tables;
}

// == LOGIC SECTION ===================================================================

if (empty($dbUser) || empty($dbPassword) || empty($dbName)) {
    echo "Please configure your database connection." . "\n";
    exit;
}

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
} catch (PDOException $e) {
    echo "Connection to database not successful." . "\n";
    $pdo = null;
    exit;
}


$numberOfAffectedRowsTotal = 0;

foreach ($tables as $tableName => $columnNames) {
    $numberOfAffectedRowsByTable = 0;
    foreach ($columnNames as $columnName) {
        $sql = "UPDATE $tableName\n" .
                "SET $columnName = REPLACE($columnName, 'wysiwigpro/', 'ddmedia/')\n" .
                "WHERE $tableName.$columnName LIKE '%wysiwigpro/%';\n\n";
        $result = $pdo->exec($sql);
        if ($result !== false) {
            $numberOfAffectedRowsByTable += $result;
            $numberOfAffectedRowsTotal += $result;
        }
    }
    echo 'Affected rows in table "' . $tableName . '": ' . $numberOfAffectedRowsByTable . "\n";
}

echo 'Total number of affected rows: ' . $numberOfAffectedRowsTotal . "\n";
