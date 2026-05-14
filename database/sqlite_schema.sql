PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    anonymous_user_id TEXT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS components (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    nome TEXT NOT NULL,
    sku TEXT NOT NULL,
    fabricante TEXT NULL,
    cod_fabricante TEXT NULL,
    descricao TEXT NULL,
    categoria TEXT NULL,
    tags TEXT NULL,
    quantidade INTEGER NOT NULL DEFAULT 0,
    unidade TEXT NOT NULL DEFAULT 'un',
    localizacao TEXT NULL,
    tolerancia TEXT NULL,
    potencia TEXT NULL,
    tensao_max TEXT NULL,
    footprint TEXT NULL,
    custo_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    preco_medio DECIMAL(10,2) NULL,
    min_estoque INTEGER NOT NULL DEFAULT 0,
    datasheet_path TEXT NULL,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_components_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    component_id INTEGER NOT NULL,
    path TEXT NOT NULL,
    principal INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_images_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_images_component FOREIGN KEY (component_id) REFERENCES components(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    entidade TEXT NOT NULL,
    entidade_id INTEGER NULL,
    acao TEXT NOT NULL,
    delta_json TEXT NULL,
    ip TEXT NULL,
    user_agent TEXT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS stock_moves (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    component_id INTEGER NOT NULL,
    tipo TEXT NOT NULL,
    quantidade INTEGER NOT NULL,
    motivo TEXT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_moves_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_moves_component FOREIGN KEY (component_id) REFERENCES components(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    email TEXT NOT NULL,
    token_hash TEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    ip TEXT NULL,
    user_agent TEXT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL,
    ip TEXT NOT NULL,
    attempts INTEGER NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_attempt_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE (email, ip)
);

-- Indexes for SQLite (create separately)
CREATE INDEX IF NOT EXISTS idx_components_user_sku ON components(user_id, sku);
CREATE INDEX IF NOT EXISTS idx_components_user ON components(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_password_resets_user ON password_resets(user_id);
CREATE INDEX IF NOT EXISTS idx_password_resets_email ON password_resets(email);
