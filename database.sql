CREATE TABLE cursos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  descricao_curta TEXT,
  descricao_completa LONGTEXT,
  carga_horaria VARCHAR(100),
  nivel VARCHAR(100),
  preco DECIMAL(10,2) DEFAULT 0,
  proxima_data DATE NULL,
  ativo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inscricoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(30) NOT NULL,
  id_curso INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  telefone VARCHAR(100) NOT NULL,
  como_conheceu VARCHAR(255),
  data_inscricao DATETIME DEFAULT CURRENT_TIMESTAMP,
  pdf_path VARCHAR(255),
  comprovativo_path VARCHAR(255) NULL,
  status_pagamento ENUM('pendente','em_analise','pago','cancelado') DEFAULT 'pendente',
  FOREIGN KEY (id_curso) REFERENCES cursos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
