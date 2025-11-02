<?php

declare(strict_types=1);

namespace App\Config;

use App\Config\ComponentTypeStore;

/**
 * Central catalog describing supported component types and their presets.
 * Strings stay in ASCII to avoid encoding issues in environments without UTF-8.
 */
final class ComponentTypeRegistry
{
    /**
     * @var array<string, array{
     *     category: string,
     *     tag: string,
     *     description: string,
     *     base: array<string, mixed>,
     *     fields: array<int, array<string, mixed>>,
     *     presets: array<string, array{label: string, values: array<string, mixed>}>
     * }>
     */
    private static array $registry = [
        // ------------------------------------------------------------------
        // Passivos
        // ------------------------------------------------------------------
        'Resistor' => [
            'category' => 'Passivos',
            'tag' => 'RES',
            'description' => 'Resistores SMD ou axiais para limitacao de corrente.',
            'base' => [
                'nome' => 'Resistor padrao',
                'categoria' => 'Passivos',
                'unidade' => 'un',
                'quantidade' => 0,
                'min_estoque' => 20,
                'footprint' => '0603',
                'tolerancia' => '1%',
                'potencia' => '0.125W',
            ],
            'fields' => [
                ['name' => 'valor_ohm', 'label' => 'Valor (Ohm)', 'placeholder' => '10k'],
                ['name' => 'tolerancia', 'label' => 'Tolerancia', 'placeholder' => '1%'],
                ['name' => 'potencia', 'label' => 'Potencia nominal', 'placeholder' => '0.125W'],
                ['name' => 'coeficiente_temp', 'label' => 'Coeficiente termico (ppm/C)', 'placeholder' => '100'],
                ['name' => 'serie_eia', 'label' => 'Serie EIA', 'placeholder' => 'E24'],
            ],
            'presets' => [
                'res_10k_0603_1pct' => [
                    'label' => 'Resistor 10k 1% 0603',
                    'values' => [
                        'nome' => 'Resistor 10k 0603 1%',
                        'sku' => 'PASS-RES-10K-0603-1PCT',
                        'valor_ohm' => '10k',
                        'tolerancia' => '1%',
                        'potencia' => '0.125W',
                        'footprint' => '0603',
                    ],
                ],
                'res_1k_0805_1pct' => [
                    'label' => 'Resistor 1k 1% 0805',
                    'values' => [
                        'nome' => 'Resistor 1k 0805 1%',
                        'sku' => 'PASS-RES-1K-0805-1PCT',
                        'valor_ohm' => '1k',
                        'tolerancia' => '1%',
                        'potencia' => '0.25W',
                        'footprint' => '0805',
                    ],
                ],
                'res_100r_axial_5pct' => [
                    'label' => 'Resistor 100R axial 5%',
                    'values' => [
                        'nome' => 'Resistor 100R axial 5%',
                        'sku' => 'PASS-RES-100R-AXIAL-5PCT',
                        'valor_ohm' => '100',
                        'tolerancia' => '5%',
                        'potencia' => '0.25W',
                        'footprint' => 'AXIAL',
                    ],
                ],
            ],
        ],
        'Capacitor MLCC' => [
            'category' => 'Passivos',
            'tag' => 'CAP-MLCC',
            'description' => 'Capacitores ceramicos multicamadas SMD para desacoplamento.',
            'base' => [
                'nome' => 'Capacitor MLCC',
                'categoria' => 'Passivos',
                'unidade' => 'un',
                'quantidade' => 0,
                'min_estoque' => 50,
                'footprint' => '0603',
            ],
            'fields' => [
                ['name' => 'capacitancia', 'label' => 'Capacitancia', 'placeholder' => '100nF'],
                ['name' => 'tensao', 'label' => 'Tensao nominal', 'placeholder' => '50V'],
                ['name' => 'dieletrico', 'label' => 'Dieletrico', 'placeholder' => 'X7R'],
                ['name' => 'faixa_temp', 'label' => 'Faixa de temperatura', 'placeholder' => '-55C a 125C'],
            ],
            'presets' => [
                'mlcc_100nf_x7r_0603' => [
                    'label' => 'MLCC 100nF 50V X7R 0603',
                    'values' => [
                        'nome' => 'Capacitor 100nF X7R 0603',
                        'sku' => 'PASS-CAP-100NF-50V-X7R-0603',
                        'capacitancia' => '100nF',
                        'tensao' => '50V',
                        'dieletrico' => 'X7R',
                        'footprint' => '0603',
                    ],
                ],
                'mlcc_1uf_x5r_0805' => [
                    'label' => 'MLCC 1uF 25V X5R 0805',
                    'values' => [
                        'nome' => 'Capacitor 1uF X5R 0805',
                        'sku' => 'PASS-CAP-1UF-25V-X5R-0805',
                        'capacitancia' => '1uF',
                        'tensao' => '25V',
                        'dieletrico' => 'X5R',
                        'footprint' => '0805',
                    ],
                ],
            ],
        ],
        'Capacitor eletrolitico' => [
            'category' => 'Passivos',
            'tag' => 'CAP-EL',
            'description' => 'Capacitores eletroliticos para filtragem de energia.',
            'base' => [
                'nome' => 'Capacitor eletrolitico',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'capacitancia', 'label' => 'Capacitancia', 'placeholder' => '100uF'],
                ['name' => 'tensao', 'label' => 'Tensao nominal', 'placeholder' => '25V'],
                ['name' => 'esr', 'label' => 'ESR (Ohm)', 'placeholder' => '0.1'],
                ['name' => 'formato', 'label' => 'Formato', 'placeholder' => 'Radial / SMD'],
            ],
            'presets' => [
                'el_100uf_25v_radial' => [
                    'label' => 'Eletrolitico 100uF 25V radial',
                    'values' => [
                        'nome' => 'Capacitor 100uF 25V radial',
                        'sku' => 'PASS-CAP-100UF-25V-RAD',
                        'capacitancia' => '100uF',
                        'tensao' => '25V',
                        'formato' => 'Radial',
                    ],
                ],
            ],
        ],
        'Capacitor tantalo' => [
            'category' => 'Passivos',
            'tag' => 'CAP-TAN',
            'description' => 'Capacitores de tantalo para alta densidade e estabilidade.',
            'base' => [
                'nome' => 'Capacitor de tantalo',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'capacitancia', 'label' => 'Capacitancia', 'placeholder' => '10uF'],
                ['name' => 'tensao', 'label' => 'Tensao nominal', 'placeholder' => '16V'],
                ['name' => 'esr', 'label' => 'ESR (Ohm)', 'placeholder' => '0.2'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'Case A / B / C'],
            ],
            'presets' => [
                'tan_10uf_16v' => [
                    'label' => 'Tantalo 10uF 16V Case A',
                    'values' => [
                        'nome' => 'Capacitor tantalo 10uF',
                        'sku' => 'PASS-CAP-10UF-16V-TAN-A',
                        'capacitancia' => '10uF',
                        'tensao' => '16V',
                        'footprint' => 'CASE A',
                    ],
                ],
            ],
        ],
        'Capacitor filme' => [
            'category' => 'Passivos',
            'tag' => 'CAP-FILM',
            'description' => 'Capacitores de filme para audio e alta tensao.',
            'base' => [
                'nome' => 'Capacitor filme',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'capacitancia', 'label' => 'Capacitancia', 'placeholder' => '100nF'],
                ['name' => 'tensao', 'label' => 'Tensao nominal', 'placeholder' => '275VAC'],
                ['name' => 'tipo_filme', 'label' => 'Tipo de filme', 'placeholder' => 'PET / PP / PPS'],
            ],
            'presets' => [
                'film_100nf_275vac' => [
                    'label' => 'Filme 100nF 275VAC X2',
                    'values' => [
                        'nome' => 'Capacitor filme 100nF 275VAC',
                        'sku' => 'PASS-CAP-100NF-275VAC-X2',
                        'capacitancia' => '100nF',
                        'tensao' => '275VAC',
                        'tipo_filme' => 'PP',
                    ],
                ],
            ],
        ],
        'Indutor' => [
            'category' => 'Passivos',
            'tag' => 'IND',
            'description' => 'Indutores para filtros e conversores DC-DC.',
            'base' => [
                'nome' => 'Indutor padrao',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'indutancia', 'label' => 'Indutancia', 'placeholder' => '10uH'],
                ['name' => 'corrente_max', 'label' => 'Corrente maxima', 'placeholder' => '1.5A'],
                ['name' => 'resistencia_dc', 'label' => 'Resistencia DC', 'placeholder' => '0.1 Ohm'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'CD54 / Toroidal'],
            ],
            'presets' => [
                'ind_10uh_cd54' => [
                    'label' => 'Indutor 10uH 1.5A CD54',
                    'values' => [
                        'nome' => 'Indutor 10uH CD54',
                        'sku' => 'PASS-IND-10UH-CD54',
                        'indutancia' => '10uH',
                        'corrente_max' => '1.5A',
                        'footprint' => 'CD54',
                    ],
                ],
            ],
        ],
        'Cristal' => [
            'category' => 'Passivos',
            'tag' => 'XTAL',
            'description' => 'Cristais de quartzo para referencia de clock.',
            'base' => [
                'nome' => 'Cristal padrao',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'frequencia', 'label' => 'Frequencia', 'placeholder' => '16MHz'],
                ['name' => 'carga', 'label' => 'Capacitancia de carga', 'placeholder' => '18pF'],
                ['name' => 'estabilidade', 'label' => 'Estabilidade', 'placeholder' => '30ppm'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'HC-49S / 3.2x2.5mm'],
            ],
            'presets' => [
                'xtal_16mhz_smd' => [
                    'label' => 'Cristal 16MHz 18pF SMD',
                    'values' => [
                        'nome' => 'Cristal 16MHz SMD',
                        'sku' => 'PASS-XTAL-16MHZ-3225',
                        'frequencia' => '16MHz',
                        'carga' => '18pF',
                        'footprint' => '3.2x2.5mm',
                    ],
                ],
            ],
        ],
        'Termistor NTC' => [
            'category' => 'Passivos',
            'tag' => 'NTC',
            'description' => 'Termistores NTC para leitura de temperatura.',
            'base' => [
                'nome' => 'Termistor NTC',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'resistencia_25c', 'label' => 'Resistencia a 25C', 'placeholder' => '10k'],
                ['name' => 'beta', 'label' => 'Constante beta', 'placeholder' => '3950'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => '0603 / bead'],
            ],
            'presets' => [
                'ntc_10k_3950' => [
                    'label' => 'NTC 10k beta 3950',
                    'values' => [
                        'nome' => 'Termistor NTC 10k',
                        'sku' => 'PASS-NTC-10K-3950',
                        'resistencia_25c' => '10k',
                        'beta' => '3950',
                    ],
                ],
            ],
        ],
        'Fusivel' => [
            'category' => 'Passivos',
            'tag' => 'FUSE',
            'description' => 'Fusiveis de protecao rapida ou lenta.',
            'base' => [
                'nome' => 'Fusivel padrao',
                'categoria' => 'Passivos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'corrente', 'label' => 'Corrente nominal', 'placeholder' => '500mA'],
                ['name' => 'tensao', 'label' => 'Tensao maxima', 'placeholder' => '250VAC'],
                ['name' => 'tipo', 'label' => 'Tipo', 'placeholder' => 'Rapido / Lento / Resetavel'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => '5x20mm / SMD 1206'],
            ],
            'presets' => [
                'fuse_500ma_5x20' => [
                    'label' => 'Fusivel vidro 500mA 5x20',
                    'values' => [
                        'nome' => 'Fusivel 500mA 5x20',
                        'sku' => 'PASS-FUSE-500MA-5X20',
                        'corrente' => '500mA',
                        'tensao' => '250VAC',
                        'tipo' => 'Rapido',
                        'footprint' => '5x20mm',
                    ],
                ],
            ],
        ],

        // ------------------------------------------------------------------
        // Semicondutores
        // ------------------------------------------------------------------
        'Diodo retificacao' => [
            'category' => 'Semicondutores',
            'tag' => 'DIO',
            'description' => 'Diodos para retificacao de linha ou baixa tensao.',
            'base' => [
                'nome' => 'Diodo retificacao',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'corrente_max', 'label' => 'Corrente maxima', 'placeholder' => '1A'],
                ['name' => 'tensao_reversa', 'label' => 'Tensao reversa', 'placeholder' => '600V'],
                ['name' => 'queda_tensao', 'label' => 'Queda de tensao', 'placeholder' => '1.1V'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'DO-41 / SMA'],
            ],
            'presets' => [
                'dio_1n4007' => [
                    'label' => '1N4007 1A 1000V',
                    'values' => [
                        'nome' => 'Diodo 1N4007',
                        'sku' => 'SEM-DIO-1N4007',
                        'corrente_max' => '1A',
                        'tensao_reversa' => '1000V',
                        'footprint' => 'DO-41',
                    ],
                ],
            ],
        ],
        'Diodo Schottky' => [
            'category' => 'Semicondutores',
            'tag' => 'SCH',
            'description' => 'Diodos Schottky para comutacao rapida.',
            'base' => [
                'nome' => 'Diodo Schottky',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'corrente_max', 'label' => 'Corrente maxima', 'placeholder' => '3A'],
                ['name' => 'tensao_reversa', 'label' => 'Tensao reversa', 'placeholder' => '40V'],
                ['name' => 'queda_tensao', 'label' => 'Queda de tensao', 'placeholder' => '0.4V'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'SMA / SMC'],
            ],
            'presets' => [
                'sch_ss34' => [
                    'label' => 'SS34 3A 40V',
                    'values' => [
                        'nome' => 'Diodo Schottky SS34',
                        'sku' => 'SEM-SCH-SS34',
                        'corrente_max' => '3A',
                        'tensao_reversa' => '40V',
                        'footprint' => 'SMC',
                    ],
                ],
            ],
        ],
        'Diodo Zener' => [
            'category' => 'Semicondutores',
            'tag' => 'ZEN',
            'description' => 'Diodos Zener para regulacao de tensao.',
            'base' => [
                'nome' => 'Diodo Zener',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tensao_zener', 'label' => 'Tensao Zener', 'placeholder' => '3.3V'],
                ['name' => 'potencia', 'label' => 'Potencia', 'placeholder' => '500mW'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'DO-35 / SOD-123'],
            ],
            'presets' => [
                'zen_3v3_500mw' => [
                    'label' => 'Zener 3.3V 500mW',
                    'values' => [
                        'nome' => 'Zener 3.3V 500mW',
                        'sku' => 'SEM-ZEN-3V3-500MW',
                        'tensao_zener' => '3.3V',
                        'potencia' => '500mW',
                        'footprint' => 'DO-35',
                    ],
                ],
            ],
        ],
        'Diodo TVS' => [
            'category' => 'Semicondutores',
            'tag' => 'TVS',
            'description' => 'Diodos de supressao transiente para protecao ESD.',
            'base' => [
                'nome' => 'Diodo TVS',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tensao_standoff', 'label' => 'Tensao standoff', 'placeholder' => '5V'],
                ['name' => 'tensao_clamp', 'label' => 'Tensao clamping', 'placeholder' => '9.2V'],
                ['name' => 'potencia_pico', 'label' => 'Potencia de pico', 'placeholder' => '600W'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'SMB / SMC'],
            ],
            'presets' => [
                'tvs_smbj5' => [
                    'label' => 'TVS SMBJ5.0A',
                    'values' => [
                        'nome' => 'TVS SMBJ5.0A',
                        'sku' => 'SEM-TVS-SMBJ5',
                        'tensao_standoff' => '5V',
                        'tensao_clamp' => '9.2V',
                        'potencia_pico' => '600W',
                        'footprint' => 'SMB',
                    ],
                ],
            ],
        ],
        'Transistor BJT' => [
            'category' => 'Semicondutores',
            'tag' => 'BJT',
            'description' => 'Transistores bipolares NPN ou PNP.',
            'base' => [
                'nome' => 'Transistor BJT',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tipo', 'label' => 'Tipo', 'placeholder' => 'NPN / PNP'],
                ['name' => 'corrente_coletor', 'label' => 'Ic maxima', 'placeholder' => '500mA'],
                ['name' => 'tensao_coletor', 'label' => 'Vce maxima', 'placeholder' => '45V'],
                ['name' => 'ganho_dc', 'label' => 'Ganho (hFE)', 'placeholder' => '100'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TO-92 / SOT-23'],
            ],
            'presets' => [
                'bjt_bc337' => [
                    'label' => 'BC337 NPN TO-92',
                    'values' => [
                        'nome' => 'Transistor BC337',
                        'sku' => 'SEM-BJT-BC337',
                        'tipo' => 'NPN',
                        'corrente_coletor' => '800mA',
                        'tensao_coletor' => '45V',
                        'footprint' => 'TO-92',
                    ],
                ],
            ],
        ],
        'Transistor MOSFET' => [
            'category' => 'Semicondutores',
            'tag' => 'MOS',
            'description' => 'Transistores MOSFET canal N ou P para chaveamento.',
            'base' => [
                'nome' => 'MOSFET padrao',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'canal', 'label' => 'Tipo de canal', 'placeholder' => 'N / P'],
                ['name' => 'vds_max', 'label' => 'Vds maximo', 'placeholder' => '30V'],
                ['name' => 'id_max', 'label' => 'Id maximo', 'placeholder' => '20A'],
                ['name' => 'rds_on', 'label' => 'Rds(on)', 'placeholder' => '10mOhm'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TO-220 / SO-8'],
            ],
            'presets' => [
                'mosfet_irlz44n' => [
                    'label' => 'IRLZ44N canal N TO-220',
                    'values' => [
                        'nome' => 'MOSFET IRLZ44N',
                        'sku' => 'SEM-MOS-IRLZ44N',
                        'canal' => 'N',
                        'vds_max' => '55V',
                        'id_max' => '47A',
                        'footprint' => 'TO-220',
                    ],
                ],
                'mosfet_si2302' => [
                    'label' => 'Si2302 MOSFET N SOT-23',
                    'values' => [
                        'nome' => 'MOSFET Si2302',
                        'sku' => 'SEM-MOS-SI2302',
                        'canal' => 'N',
                        'vds_max' => '20V',
                        'id_max' => '2.6A',
                        'footprint' => 'SOT-23',
                    ],
                ],
            ],
        ],
        'Transistor IGBT' => [
            'category' => 'Semicondutores',
            'tag' => 'IGBT',
            'description' => 'Transistores IGBT para cargas de alta potencia.',
            'base' => [
                'nome' => 'IGBT padrao',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'vce_max', 'label' => 'Vce max', 'placeholder' => '600V'],
                ['name' => 'ic_max', 'label' => 'Ic max', 'placeholder' => '20A'],
                ['name' => 'frequencia', 'label' => 'Frequencia maxima', 'placeholder' => '20kHz'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TO-247'],
            ],
            'presets' => [
                'igbt_hgtg30n60' => [
                    'label' => 'IGBT 600V 60A TO-247',
                    'values' => [
                        'nome' => 'IGBT HGTG30N60',
                        'sku' => 'SEM-IGBT-HGTG30N60',
                        'vce_max' => '600V',
                        'ic_max' => '60A',
                        'footprint' => 'TO-247',
                    ],
                ],
            ],
        ],
        'Regulador LDO' => [
            'category' => 'Semicondutores',
            'tag' => 'LDO',
            'description' => 'Reguladores lineares de baixa queda.',
            'base' => [
                'nome' => 'Regulador LDO',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tensao_saida', 'label' => 'Tensao de saida', 'placeholder' => '3.3V'],
                ['name' => 'corrente_saida', 'label' => 'Corrente maxima', 'placeholder' => '500mA'],
                ['name' => 'dropout', 'label' => 'Queda (dropout)', 'placeholder' => '300mV'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'SOT-223 / SOT-23'],
            ],
            'presets' => [
                'ldo_ams1117' => [
                    'label' => 'AMS1117-3.3 SOT-223',
                    'values' => [
                        'nome' => 'LDO AMS1117-3.3',
                        'sku' => 'SEM-LDO-AMS1117-33',
                        'tensao_saida' => '3.3V',
                        'corrente_saida' => '800mA',
                        'dropout' => '1.1V',
                        'footprint' => 'SOT-223',
                    ],
                ],
                'ldo_mcp1700' => [
                    'label' => 'MCP1700-3302 SOT-23',
                    'values' => [
                        'nome' => 'LDO MCP1700 3.3V',
                        'sku' => 'SEM-LDO-MCP1700-33',
                        'tensao_saida' => '3.3V',
                        'corrente_saida' => '250mA',
                        'dropout' => '178mV',
                        'footprint' => 'SOT-23',
                    ],
                ],
            ],
        ],
        'Regulador buck' => [
            'category' => 'Semicondutores',
            'tag' => 'BUCK',
            'description' => 'Controladores ou modulos step-down.',
            'base' => [
                'nome' => 'Regulador buck',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tensao_entrada', 'label' => 'Tensao de entrada', 'placeholder' => '4.5-40V'],
                ['name' => 'tensao_saida', 'label' => 'Tensao de saida', 'placeholder' => '1.2-35V'],
                ['name' => 'corrente_saida', 'label' => 'Corrente maxima', 'placeholder' => '3A'],
                ['name' => 'frequencia', 'label' => 'Frequencia chaveamento', 'placeholder' => '150kHz'],
            ],
            'presets' => [
                'buck_lm2596' => [
                    'label' => 'Modulo LM2596 3A',
                    'values' => [
                        'nome' => 'Modulo buck LM2596',
                        'sku' => 'SEM-BUCK-LM2596',
                        'tensao_entrada' => '4.5-40V',
                        'tensao_saida' => '1.2-35V',
                        'corrente_saida' => '3A',
                        'frequencia' => '150kHz',
                    ],
                ],
            ],
        ],
        'Regulador boost' => [
            'category' => 'Semicondutores',
            'tag' => 'BOOST',
            'description' => 'Conversores DC-DC step-up.',
            'base' => [
                'nome' => 'Regulador boost',
                'categoria' => 'Semicondutores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tensao_entrada', 'label' => 'Tensao de entrada', 'placeholder' => '2-24V'],
                ['name' => 'tensao_saida', 'label' => 'Tensao de saida', 'placeholder' => '5-28V'],
                ['name' => 'corrente_saida', 'label' => 'Corrente maxima', 'placeholder' => '2A'],
                ['name' => 'eficiencia', 'label' => 'Eficiencia', 'placeholder' => '90%'],
            ],
            'presets' => [
                'boost_mt3608' => [
                    'label' => 'Modulo MT3608 2A',
                    'values' => [
                        'nome' => 'Modulo boost MT3608',
                        'sku' => 'SEM-BOOST-MT3608',
                        'tensao_entrada' => '2-24V',
                        'tensao_saida' => '5-28V',
                        'corrente_saida' => '2A',
                        'eficiencia' => '90%',
                    ],
                ],
            ],
        ],

        // ------------------------------------------------------------------
        // CI / Displays / Sensores / Modulos / Conectores / Eletromecanicos
        // ------------------------------------------------------------------
        'Microcontrolador' => [
            'category' => 'CI/IC',
            'tag' => 'MCU',
            'description' => 'Microcontroladores de 8/32 bits.',
            'base' => [
                'nome' => 'Microcontrolador',
                'categoria' => 'CI/IC',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'part_number', 'label' => 'Part number', 'placeholder' => 'ATmega328P-AU'],
                ['name' => 'familia', 'label' => 'Familia', 'placeholder' => 'AVR / ARM / ESP'],
                ['name' => 'pins', 'label' => 'Numero de pinos', 'placeholder' => '32'],
                ['name' => 'tensao_oper', 'label' => 'Tensao operacao', 'placeholder' => '1.8-5.5V'],
                ['name' => 'clock_max', 'label' => 'Clock max', 'placeholder' => '20MHz'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TQFP-32 / QFN'],
            ],
            'presets' => [
                'mcu_atmega328p' => [
                    'label' => 'ATmega328P-AU TQFP-32',
                    'values' => [
                        'nome' => 'ATmega328P-AU',
                        'sku' => 'IC-MCU-ATMEGA328P-TQFP32',
                        'part_number' => 'ATMEGA328P-AU',
                        'familia' => 'AVR',
                        'pins' => 32,
                        'tensao_oper' => '1.8-5.5V',
                        'clock_max' => '20MHz',
                        'footprint' => 'TQFP-32',
                    ],
                ],
            ],
        ],
        'Display 7-seg' => [
            'category' => 'Displays',
            'tag' => '7SEG',
            'description' => 'Displays de 7 segmentos.',
            'base' => [
                'nome' => 'Display 7-seg',
                'categoria' => 'Displays',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'digitos', 'label' => 'Digitos', 'placeholder' => '1 / 2 / 4'],
                ['name' => 'comum', 'label' => 'Tipo comum', 'placeholder' => 'Anodo / Catodo'],
                ['name' => 'cor', 'label' => 'Cor', 'placeholder' => 'Vermelho'],
                ['name' => 'if_segmento', 'label' => 'Corrente por segmento', 'placeholder' => '10-20mA'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TH / SMD'],
            ],
            'presets' => [
                '7seg_4d_anodo_red' => [
                    'label' => '7-seg 4 digitos anodo vermelho',
                    'values' => [
                        'nome' => 'Display 7-seg 4D AC vermelho',
                        'sku' => 'DSP-7SEG-4D-AC-RED-TH',
                        'digitos' => 4,
                        'comum' => 'Anodo',
                        'cor' => 'Vermelho',
                        'footprint' => 'TH',
                    ],
                ],
            ],
        ],
        'LED' => [
            'category' => 'Displays',
            'tag' => 'LED',
            'description' => 'LED padrao ou SMD.',
            'base' => [
                'nome' => 'LED padrao',
                'categoria' => 'Displays',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'cor', 'label' => 'Cor', 'placeholder' => 'Vermelho / Verde / Azul'],
                ['name' => 'vf', 'label' => 'Tensao direta (Vf)', 'placeholder' => '2.0V'],
                ['name' => 'if', 'label' => 'Corrente direta (If)', 'placeholder' => '20mA'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => '5mm / 0603 / 1206'],
            ],
            'presets' => [
                'led_5mm_red' => [
                    'label' => 'LED 5mm vermelho',
                    'values' => [
                        'nome' => 'LED 5mm vermelho',
                        'sku' => 'DSP-LED-5MM-RED',
                        'cor' => 'Vermelho',
                        'vf' => '2.0V',
                        'if' => '20mA',
                        'footprint' => '5mm',
                    ],
                ],
            ],
        ],
        'Conector JST' => [
            'category' => 'Conectores',
            'tag' => 'JST',
            'description' => 'Conectores JST linha PH/XH.',
            'base' => [
                'nome' => 'Conector JST',
                'categoria' => 'Conectores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'serie', 'label' => 'Serie', 'placeholder' => 'PH / XH'],
                ['name' => 'vias', 'label' => 'Numero de vias', 'placeholder' => '2-6'],
                ['name' => 'montagem', 'label' => 'Montagem', 'placeholder' => 'TH / SMD'],
                ['name' => 'pitch', 'label' => 'Pitch', 'placeholder' => '2.0mm / 2.54mm'],
            ],
            'presets' => [
                'jst_xh_2p_th' => [
                    'label' => 'JST XH 2 vias TH',
                    'values' => [
                        'nome' => 'JST XH 2P TH',
                        'sku' => 'CON-JST-XH-2P-TH',
                        'serie' => 'XH',
                        'vias' => 2,
                        'montagem' => 'TH',
                        'pitch' => '2.54mm',
                    ],
                ],
            ],
        ],
        'Rele' => [
            'category' => 'Eletromecanicos',
            'tag' => 'RELAY',
            'description' => 'Rele eletromecanico ou SSR.',
            'base' => [
                'nome' => 'Rele padrao',
                'categoria' => 'Eletromecanicos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'bobina', 'label' => 'Tensao bobina', 'placeholder' => '5V / 12V'],
                ['name' => 'contatos', 'label' => 'Tipo de contatos', 'placeholder' => 'SPDT / DPDT'],
                ['name' => 'corrente_contato', 'label' => 'Corrente contatos', 'placeholder' => '10A'],
                ['name' => 'footprint', 'label' => 'Encapsulamento', 'placeholder' => 'TH / modulo'],
            ],
            'presets' => [
                'rele_5v_spdt_10a' => [
                    'label' => 'Rele 5V SPDT 10A',
                    'values' => [
                        'nome' => 'Rele 5V SPDT 10A',
                        'sku' => 'EM-RELAY-5V-SPDT-10A',
                        'bobina' => '5V',
                        'contatos' => 'SPDT',
                        'corrente_contato' => '10A',
                        'footprint' => 'TH',
                    ],
                ],
            ],
        ],
        'Botao tatil' => [
            'category' => 'Eletromecanicos',
            'tag' => 'SW-TAC',
            'description' => 'Botoes taticos SMD/TH.',
            'base' => [
                'nome' => 'Botao tatil',
                'categoria' => 'Eletromecanicos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'forca', 'label' => 'Forca de acionamento', 'placeholder' => '160gf'],
                ['name' => 'altura', 'label' => 'Altura', 'placeholder' => '4.3mm / 7mm'],
                ['name' => 'montagem', 'label' => 'Montagem', 'placeholder' => 'SMD / TH'],
            ],
            'presets' => [
                'tact_6x6_7mm' => [
                    'label' => 'Tatil 6x6mm H=7mm TH',
                    'values' => [
                        'nome' => 'Botao tatil 6x6x7 TH',
                        'sku' => 'EM-SW-TAC-6X6-H7-TH',
                        'forca' => '160gf',
                        'altura' => '7mm',
                        'montagem' => 'TH',
                    ],
                ],
            ],
        ],
        'Sensor temperatura' => [
            'category' => 'Sensores',
            'tag' => 'TEMP',
            'description' => 'Sensores de temperatura digitais/analogicos.',
            'base' => [
                'nome' => 'Sensor de temperatura',
                'categoria' => 'Sensores',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tipo', 'label' => 'Tipo', 'placeholder' => 'Analogico / 1-Wire / I2C'],
                ['name' => 'faixa', 'label' => 'Faixa de medicao', 'placeholder' => '-55C a 125C'],
                ['name' => 'precisao', 'label' => 'Precisao', 'placeholder' => '±0.5C'],
                ['name' => 'tensao', 'label' => 'Tensao operacao', 'placeholder' => '3.3-5V'],
            ],
            'presets' => [
                'ds18b20' => [
                    'label' => 'DS18B20 1-Wire',
                    'values' => [
                        'nome' => 'Sensor DS18B20',
                        'sku' => 'SENS-TEMP-DS18B20',
                        'tipo' => '1-Wire',
                        'faixa' => '-55C a 125C',
                        'precisao' => '±0.5C',
                        'tensao' => '3.0-5.5V',
                    ],
                ],
            ],
        ],
        'Modulo WiFi' => [
            'category' => 'Modulos',
            'tag' => 'WIFI',
            'description' => 'Modulos WiFi para IoT.',
            'base' => [
                'nome' => 'Modulo WiFi',
                'categoria' => 'Modulos',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'chip', 'label' => 'Chipset', 'placeholder' => 'ESP8266 / ESP32'],
                ['name' => 'interface', 'label' => 'Interface', 'placeholder' => 'UART / SPI'],
                ['name' => 'antena', 'label' => 'Antena', 'placeholder' => 'Onboard / U.FL'],
            ],
            'presets' => [
                'esp8266_esp12f' => [
                    'label' => 'ESP8266-12F',
                    'values' => [
                        'nome' => 'ESP8266 ESP-12F',
                        'sku' => 'MOD-WIFI-ESP8266-12F',
                        'chip' => 'ESP8266',
                        'interface' => 'UART',
                        'antena' => 'Onboard',
                    ],
                ],
            ],
        ],
        'Display OLED' => [
            'category' => 'Displays',
            'tag' => 'OLED',
            'description' => 'Displays OLED pequenos para UI.',
            'base' => [
                'nome' => 'Display OLED',
                'categoria' => 'Displays',
                'unidade' => 'un',
            ],
            'fields' => [
                ['name' => 'tamanho', 'label' => 'Tamanho', 'placeholder' => '0.96 in'],
                ['name' => 'resolucao', 'label' => 'Resolucao', 'placeholder' => '128x64'],
                ['name' => 'interface', 'label' => 'Interface', 'placeholder' => 'I2C / SPI'],
                ['name' => 'controlador', 'label' => 'Controlador', 'placeholder' => 'SSD1306'],
            ],
            'presets' => [
                'oled_096_i2c' => [
                    'label' => 'OLED 0.96 128x64 I2C',
                    'values' => [
                        'nome' => 'OLED 0.96 128x64 I2C',
                        'sku' => 'DSP-OLED-096-128X64-I2C',
                        'tamanho' => '0.96 in',
                        'resolucao' => '128x64',
                        'interface' => 'I2C',
                        'controlador' => 'SSD1306',
                    ],
                ],
            ],
        ],
    ];
    /**
     * @var array<string, array<string, mixed>>|null
     */
    private static ?array $loaded = null;

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function data(): array
    {
        if (self::$loaded !== null) {
            return self::$loaded;
        }

        $merged = self::$registry;

        try {
            $custom = ComponentTypeStore::all();
            foreach ($custom as $type => $definition) {
                $merged[$type] = $definition;
            }
        } catch (\Throwable) {
            // ignorar erros de leitura dos tipos customizados
        }

        self::$loaded = $merged;
        return self::$loaded;
    }

    public static function reload(): void
    {
        self::$loaded = null;
    }

    // ----------------------------------------------------------------------
    // API publica de acesso ao catalogo
    // ----------------------------------------------------------------------

    /** @return array<string, mixed>|null */
    public static function get(string $type): ?array
    {
        return self::data()[$type] ?? null;
    }

    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return self::data();
    }

    /** @return list<string> */
    public static function categories(): array
    {
        $cats = [];
        foreach (self::data() as $def) {
            $cats[] = $def['category'];
        }
        $cats = array_values(array_unique($cats));
        sort($cats, SORT_NATURAL | SORT_FLAG_CASE);
        return $cats;
    }

    /**
     * Group types by category for selection screens.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function groupedByCategory(): array
    {
        $grouped = [];
        foreach (self::data() as $name => $definition) {
            $category = $definition['category'] ?? 'Outros';
            $grouped[$category][] = [
                'type' => $name,
                'tag' => $definition['tag'] ?? null,
                'description' => $definition['description'] ?? null,
            ];
        }

        ksort($grouped, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($grouped as &$items) {
            usort(
                $items,
                static fn(array $a, array $b): int => strcasecmp($a['type'], $b['type'])
            );
        }
        unset($items);

        return $grouped;
    }

    /** @return list<string> */
    public static function listTypes(?string $category = null): array
    {
        if ($category === null) {
            return array_keys(self::data());
        }
        $out = [];
        foreach (self::data() as $name => $def) {
            if ($def['category'] === $category) {
                $out[] = $name;
            }
        }
        return $out;
    }

    /** @return array<int, array<string, mixed>> */
    public static function fields(string $type): array
    {
        return self::data()[$type]['fields'] ?? [];
    }

    /** @return array<string, mixed> */
    public static function base(string $type): array
    {
        return self::data()[$type]['base'] ?? [];
    }

    /** @return array<string, array{label:string, values:array<string,mixed>}> */
    public static function presets(string $type): array
    {
        return self::data()[$type]['presets'] ?? [];
    }

    /** @return array<string, mixed>|null */
    public static function preset(string $type, string $key): ?array
    {
        return self::data()[$type]['presets'][$key]['values'] ?? null;
    }

    /**
     * Build a default item array for given type, optionally applying a preset.
     * Base fields are merged with preset values (preset overrides base).
     *
     * @return array<string, mixed>
     */
    public static function buildDefault(string $type, ?string $presetKey = null): array
    {
        $base = self::base($type);
        if ($presetKey !== null) {
            $preset = self::preset($type, $presetKey);
            if ($preset) {
                $base = array_replace($base, $preset);
            }
        }
        // Ensure common keys exist
        $base += [
            'sku' => $base['sku'] ?? '',
            'fabricante' => $base['fabricante'] ?? '',
            'cod_fabricante' => $base['cod_fabricante'] ?? '',
            'descricao' => $base['descricao'] ?? '',
            'tags' => $base['tags'] ?? [],
            'localizacao' => $base['localizacao'] ?? '',
        ];
        return $base;
    }

    /**
     * Simple search over type name, tag and description.
     *
     * @return list<string> matched type names
     */
    public static function search(string $q): array
    {
        $q = strtolower(trim($q));
        if ($q === '') {
            return array_keys(self::data());
        }
        $out = [];
        foreach (self::data() as $name => $def) {
            $hay = strtolower($name . ' ' . $def['tag'] . ' ' . $def['description'] . ' ' . $def['category']);
            if (strpos($hay, $q) !== false) {
                $out[] = $name;
            }
        }
        return $out;
    }
}