START TRANSACTION;

INSERT INTO users (name, email, password_hash, created_at, updated_at)
VALUES (
    'Demo User',
    'demo@local',
    '$2b$12$.cP4da4ReQDE.gS0N0G6Be6yq9jwZvlLwbRIXhTdpFs2BirQt6Xs2',
    NOW(),
    NOW()
);

SET @demo_user_id := LAST_INSERT_ID();

INSERT INTO components (
    user_id, nome, sku, fabricante, cod_fabricante, descricao, categoria, tags,
    quantidade, unidade, localizacao, tolerancia, potencia, tensao_max, footprint,
    custo_unitario, preco_medio, min_estoque, datasheet_path, created_at, updated_at
) VALUES
(@demo_user_id, 'Resistor 1K 1%', 'COMP-RES-1K', 'Yageo', 'RC0603FR-071KL', 'Resistor de filme espesso 0603 1K', 'Resistores', 'resistor,0603,passivo', 2400, 'un', 'A1-01', '1%', '0.125W', '150V', '0603', 0.03, 0.05, 500, NULL, NOW(), NOW()),
(@demo_user_id, 'Capacitor 100nF', 'COMP-CAP-100NF', 'Murata', 'GRM188R71C104KA01D', 'Capacitor ceramico multilayer 0603 100nF', 'Capacitores', 'capacitor,100nf,0603', 1800, 'un', 'A1-02', '10%', 'NA', '50V', '0603', 0.04, 0.06, 400, NULL, NOW(), NOW()),
(@demo_user_id, 'Microcontrolador ATmega328P', 'COMP-MCU-ATMEGA328P', 'Microchip', 'ATMEGA328P-AU', 'MCU 8 bits 32KB flash TQFP-32', 'Microcontroladores', 'microcontrolador,atmega', 120, 'un', 'B2-10', 'NA', 'NA', '5.5V', 'TQFP-32', 3.20, 3.50, 50, NULL, NOW(), NOW()),
(@demo_user_id, 'Regulador LDO 3.3V', 'COMP-LDO-AP7333', 'Diodes Inc', 'AP7333-33SAG-7', 'Regulador LDO 300mA 3.3V SOT-223', 'Fontes', 'ldo,3v3,power', 320, 'un', 'B2-05', 'NA', 'NA', '10V', 'SOT-223', 0.28, 0.35, 80, NULL, NOW(), NOW()),
(@demo_user_id, 'Conector USB-C Reversivel', 'COMP-CON-USBC', 'Amphenol', '1240180E212A', 'Conector USB-C 16 pinos solda', 'Conectores', 'usb-c,conector', 90, 'un', 'C3-07', 'NA', 'NA', '20V', 'SMD', 1.80, 2.10, 20, NULL, NOW(), NOW()),
(@demo_user_id, 'Sensor Temperatura LM35', 'COMP-SEN-LM35', 'Texas Instruments', 'LM35DZ/NOPB', 'Sensor de temperatura analogico TO-92', 'Sensores', 'sensor,temperatura', 45, 'un', 'C1-03', 'NA', 'NA', '30V', 'TO-92', 1.10, 1.30, 15, NULL, NOW(), NOW()),
(@demo_user_id, 'MOSFET Canal N 30V', 'COMP-MOS-AO3400', 'Alpha & Omega', 'AO3400A', 'MOSFET canal N 30V 5.7A SOT-23', 'Semicondutores', 'mosfet,smd,canal-n', 600, 'un', 'B4-04', 'NA', 'NA', '30V', 'SOT-23', 0.19, 0.22, 150, NULL, NOW(), NOW()),
(@demo_user_id, 'Diodo Schottky 1A', 'COMP-DIO-SS14', 'Various', 'SS14', 'Diodo Schottky SMD SMA 1A 40V', 'Diodos', 'diodo,schottky', 420, 'un', 'B4-06', 'NA', 'NA', '40V', 'SMA', 0.07, 0.09, 120, NULL, NOW(), NOW()),
(@demo_user_id, 'Display OLED 0.96"', 'COMP-DSP-OLED096', 'Newhaven', 'NHD-0.96-12864', 'Display OLED 128x64 I2C', 'Displays', 'display,oled,i2c', 35, 'un', 'D2-01', 'NA', 'NA', '5V', 'SMD', 6.50, 6.90, 10, NULL, NOW(), NOW()),
(@demo_user_id, 'Modulo ESP32-WROOM-32', 'COMP-WIFI-ESP32', 'Espressif', 'ESP32-WROOM-32D', 'Modulo Wi-Fi ESP32 dual core', 'Comunicacao', 'wifi,esp32,modulo', 60, 'un', 'D2-05', 'NA', 'NA', '3.6V', 'SMD', 3.80, 4.20, 20, NULL, NOW(), NOW());

INSERT INTO stock_moves (user_id, component_id, tipo, quantidade, motivo, created_at) VALUES
(@demo_user_id, (SELECT id FROM components WHERE sku = 'COMP-RES-1K' LIMIT 1), 'entrada', 2400, 'Carga inicial de estoque', NOW()),
(@demo_user_id, (SELECT id FROM components WHERE sku = 'COMP-CAP-100NF' LIMIT 1), 'entrada', 1800, 'Carga inicial de estoque', NOW()),
(@demo_user_id, (SELECT id FROM components WHERE sku = 'COMP-MCU-ATMEGA328P' LIMIT 1), 'entrada', 120, 'Carga inicial de estoque', NOW());

COMMIT;