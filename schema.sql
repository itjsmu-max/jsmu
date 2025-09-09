-- EPKWT schema (MySQL 8+)
CREATE TABLE IF NOT EXISTS projects (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) UNIQUE,
  name VARCHAR(191) NOT NULL,
  location VARCHAR(191) NULL,
  address VARCHAR(255) NULL,
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS employees (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nik VARCHAR(30) UNIQUE,
  full_name VARCHAR(191) NOT NULL,
  birth_place VARCHAR(100) NULL,
  birth_date DATE NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(30) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS employment_assignments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT UNSIGNED NOT NULL,
  project_id BIGINT UNSIGNED NOT NULL,
  position VARCHAR(100) NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  base_salary BIGINT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_emp_proj (employee_id, project_id),
  CONSTRAINT fk_ea_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  CONSTRAINT fk_ea_proj FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contract_templates (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(191) UNIQUE,
  body_html LONGTEXT NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS contracts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT UNSIGNED NOT NULL,
  project_id BIGINT UNSIGNED NOT NULL,
  template_id BIGINT UNSIGNED NOT NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  base_salary BIGINT UNSIGNED DEFAULT 0,
  allowance BIGINT UNSIGNED DEFAULT 0,
  location VARCHAR(191) NULL,
  status VARCHAR(30) DEFAULT 'DRAFT',
  contract_no VARCHAR(100) UNIQUE NULL,
  file_path VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_status_end (status, end_date),
  CONSTRAINT fk_c_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  CONSTRAINT fk_c_proj FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  CONSTRAINT fk_c_tpl FOREIGN KEY (template_id) REFERENCES contract_templates(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contract_signatures (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  contract_id BIGINT UNSIGNED NOT NULL,
  signer_role VARCHAR(30) NOT NULL,
  path VARCHAR(255) NOT NULL,
  signer_name VARCHAR(191) NULL,
  signed_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_sign (contract_id, signer_role),
  CONSTRAINT fk_cs_c FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
);

-- Optional: add username to users if missing
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(191) NULL UNIQUE AFTER id;

-- Seed minimal template
INSERT INTO contract_templates (name, body_html, created_at, updated_at)
VALUES ('PKWT Default',
'<div style="font-family:Times New Roman; font-size:12pt; line-height:1.4"><h3 style="text-align:center">PKWT</h3><p>Pekerja: <b><span style="color:red">{{ employee.full_name }}</span></b> (NIK: <span style="color:red">{{ employee.nik }}</span>)</p><p>Proyek: <b><span style="color:red">{{ project.name }}</span></b></p><p>Periode: <b><span style="color:red">{{ contract.start_date }}</span></b> s.d. <b><span style="color:red">{{ contract.end_date }}</span></b></p><p>Gaji: Rp <span style="color:red">{{ contract.base_salary }}</span> | Tunjangan: Rp <span style="color:red">{{ contract.allowance }}</span></p><p>Lokasi: <span style="color:red">{{ contract.location }}</span></p><p>No: <span style="color:red">{{ contract.contract_no }}</span></p><hr/><div style="display:flex;justify-content:space-between;"><div><p>Pekerja</p><img src="{{ signature.employee }}" style="height:80px"/><p><b>{{ employee.full_name }}</b></p></div><div><p>Perusahaan</p><img src="{{ signature.hr }}" style="height:80px"/><p><b>{{ perusahaan_perwakilan }}</b></p></div></div></div>',
NOW(), NOW())
ON DUPLICATE KEY UPDATE body_html=VALUES(body_html), updated_at=NOW();
