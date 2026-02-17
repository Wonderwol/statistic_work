/* Кнопки управления видом */
.view-controls {
  display: flex;
  gap: 10px;
  margin-left: auto;
}

.view-btn {
  padding: 10px 20px;
  border: 2px solid var(--medium-gray);
  background-color: var(--white);
  color: var(--dark-gray);
  cursor: pointer;
  border-radius: var(--border-radius);
  font-size: 14px;
  font-weight: 600;
  transition: var(--transition);
  min-width: 100px;
  text-align: center;
}

.view-btn:hover {
  background-color: var(--primary-hover);
  color: #000;
  border-color: var(--primary-hover);
}

.view-btn.active {
  background-color: var(--primary-color);
  color: var(--white);
  border-color: var(--primary-color);
}

.view-btn.active:hover {
  background-color: #5a373d;
  border-color: #5a373d;
}

/* Визуальные стили кнопок (без layout) */
.btn-primary,
.btn-secondary {
  padding: 12px 30px;
  border: none;
  border-radius: var(--border-radius);
  cursor: pointer;
  font-size: 15px;
  font-weight: 600;
  transition: var(--transition);
}

.btn-primary {
  background-color: var(--primary-color);
  color: var(--white);
}

.btn-primary:hover {
  background-color: var(--primary-hover);
  color: #000;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-secondary {
  background-color: #e0e0e0;
  color: var(--dark-gray);
}

.btn-secondary:hover {
  background-color: #d0d0d0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Спиннер для кнопки загрузки */
.btn-primary.loading {
  position: relative;
  color: transparent !important;
}

.btn-primary.loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin: -10px 0 0 -10px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.info-link{
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(0,0,0,0.04);
  border: 1px solid rgba(0,0,0,0.08);
  text-decoration: none;
  margin-right: 10px;
}

.info-link img{
  width: 18px;
  height: 18px;
  display: block;
}
