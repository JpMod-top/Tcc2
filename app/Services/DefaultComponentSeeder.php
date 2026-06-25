<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Component;
use App\Models\StockMove;

class DefaultComponentSeeder
{
    public static function ensureSeededForUser(int $userId): void
    {
        $activeComponents = (int)DB::run(
            'SELECT COUNT(*) FROM components WHERE user_id = :user_id AND deleted_at IS NULL',
            ['user_id' => $userId]
        )->fetchColumn();

        if ($activeComponents > 0) {
            return;
        }

        DB::transaction(static function () use ($userId): void {
            foreach (self::rows() as $row) {
                $componentId = Component::create($userId, $row);
                StockMove::record($userId, $componentId, 'entrada', (int)$row['quantidade'], 'Carga inicial de estoque');
            }
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function rows(): array
    {
        return [
            [
                'nome' => 'Resistor 1K 1%',
                'sku' => 'COMP-RES-1K',
                'fabricante' => 'Yageo',
                'cod_fabricante' => 'RC0603FR-071KL',
                'descricao' => 'Resistor de filme espesso 0603 1K',
                'categoria' => 'Resistores',
                'tags' => 'resistor,0603,passivo',
                'quantidade' => 2400,
                'unidade' => 'un',
                'localizacao' => 'A1-01',
                'tolerancia' => '1%',
                'potencia' => '0.125W',
                'tensao_max' => '150V',
                'footprint' => '0603',
                'custo_unitario' => 0.03,
                'preco_medio' => 0.05,
                'min_estoque' => 500,
            ],
            [
                'nome' => 'Capacitor 100nF',
                'sku' => 'COMP-CAP-100NF',
                'fabricante' => 'Murata',
                'cod_fabricante' => 'GRM188R71C104KA01D',
                'descricao' => 'Capacitor ceramico multilayer 0603 100nF',
                'categoria' => 'Capacitores',
                'tags' => 'capacitor,100nf,0603',
                'quantidade' => 1800,
                'unidade' => 'un',
                'localizacao' => 'A1-02',
                'tolerancia' => '10%',
                'potencia' => 'NA',
                'tensao_max' => '50V',
                'footprint' => '0603',
                'custo_unitario' => 0.04,
                'preco_medio' => 0.06,
                'min_estoque' => 400,
            ],
            [
                'nome' => 'Microcontrolador ATmega328P',
                'sku' => 'COMP-MCU-ATMEGA328P',
                'fabricante' => 'Microchip',
                'cod_fabricante' => 'ATMEGA328P-AU',
                'descricao' => 'MCU 8 bits 32KB flash TQFP-32',
                'categoria' => 'Microcontroladores',
                'tags' => 'microcontrolador,atmega',
                'quantidade' => 120,
                'unidade' => 'un',
                'localizacao' => 'B2-10',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '5.5V',
                'footprint' => 'TQFP-32',
                'custo_unitario' => 3.20,
                'preco_medio' => 3.50,
                'min_estoque' => 50,
            ],
            [
                'nome' => 'Regulador LDO 3.3V',
                'sku' => 'COMP-LDO-AP7333',
                'fabricante' => 'Diodes Inc',
                'cod_fabricante' => 'AP7333-33SAG-7',
                'descricao' => 'Regulador LDO 300mA 3.3V SOT-223',
                'categoria' => 'Fontes',
                'tags' => 'ldo,3v3,power',
                'quantidade' => 320,
                'unidade' => 'un',
                'localizacao' => 'B2-05',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '10V',
                'footprint' => 'SOT-223',
                'custo_unitario' => 0.28,
                'preco_medio' => 0.35,
                'min_estoque' => 80,
            ],
            [
                'nome' => 'Conector USB-C Reversivel',
                'sku' => 'COMP-CON-USBC',
                'fabricante' => 'Amphenol',
                'cod_fabricante' => '1240180E212A',
                'descricao' => 'Conector USB-C 16 pinos solda',
                'categoria' => 'Conectores',
                'tags' => 'usb-c,conector',
                'quantidade' => 90,
                'unidade' => 'un',
                'localizacao' => 'C3-07',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '20V',
                'footprint' => 'SMD',
                'custo_unitario' => 1.80,
                'preco_medio' => 2.10,
                'min_estoque' => 20,
            ],
            [
                'nome' => 'Sensor Temperatura LM35',
                'sku' => 'COMP-SEN-LM35',
                'fabricante' => 'Texas Instruments',
                'cod_fabricante' => 'LM35DZ/NOPB',
                'descricao' => 'Sensor de temperatura analogico TO-92',
                'categoria' => 'Sensores',
                'tags' => 'sensor,temperatura',
                'quantidade' => 45,
                'unidade' => 'un',
                'localizacao' => 'C1-03',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '30V',
                'footprint' => 'TO-92',
                'custo_unitario' => 1.10,
                'preco_medio' => 1.30,
                'min_estoque' => 15,
            ],
            [
                'nome' => 'MOSFET Canal N 30V',
                'sku' => 'COMP-MOS-AO3400',
                'fabricante' => 'Alpha & Omega',
                'cod_fabricante' => 'AO3400A',
                'descricao' => 'MOSFET canal N 30V 5.7A SOT-23',
                'categoria' => 'Semicondutores',
                'tags' => 'mosfet,smd,canal-n',
                'quantidade' => 600,
                'unidade' => 'un',
                'localizacao' => 'B4-04',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '30V',
                'footprint' => 'SOT-23',
                'custo_unitario' => 0.19,
                'preco_medio' => 0.22,
                'min_estoque' => 150,
            ],
            [
                'nome' => 'Diodo Schottky 1A',
                'sku' => 'COMP-DIO-SS14',
                'fabricante' => 'Various',
                'cod_fabricante' => 'SS14',
                'descricao' => 'Diodo Schottky SMD SMA 1A 40V',
                'categoria' => 'Diodos',
                'tags' => 'diodo,schottky',
                'quantidade' => 420,
                'unidade' => 'un',
                'localizacao' => 'B4-06',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '40V',
                'footprint' => 'SMA',
                'custo_unitario' => 0.07,
                'preco_medio' => 0.09,
                'min_estoque' => 120,
            ],
            [
                'nome' => 'Display OLED 0.96"',
                'sku' => 'COMP-DSP-OLED096',
                'fabricante' => 'Newhaven',
                'cod_fabricante' => 'NHD-0.96-12864',
                'descricao' => 'Display OLED 128x64 I2C',
                'categoria' => 'Displays',
                'tags' => 'display,oled,i2c',
                'quantidade' => 35,
                'unidade' => 'un',
                'localizacao' => 'D2-01',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '5V',
                'footprint' => 'SMD',
                'custo_unitario' => 6.50,
                'preco_medio' => 6.90,
                'min_estoque' => 10,
            ],
            [
                'nome' => 'Modulo ESP32-WROOM-32',
                'sku' => 'COMP-WIFI-ESP32',
                'fabricante' => 'Espressif',
                'cod_fabricante' => 'ESP32-WROOM-32D',
                'descricao' => 'Modulo Wi-Fi ESP32 dual core',
                'categoria' => 'Comunicacao',
                'tags' => 'wifi,esp32,modulo',
                'quantidade' => 60,
                'unidade' => 'un',
                'localizacao' => 'D2-05',
                'tolerancia' => 'NA',
                'potencia' => 'NA',
                'tensao_max' => '3.6V',
                'footprint' => 'SMD',
                'custo_unitario' => 3.80,
                'preco_medio' => 4.20,
                'min_estoque' => 20,
            ],
        ];
    }
}
