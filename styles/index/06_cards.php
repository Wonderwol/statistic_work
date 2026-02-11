/* Сетка карточек: адаптивно без @media */
.statistics {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
  gap: 12px;
  margin-top: 12px;
  margin-bottom: 12px;
  align-items: stretch;
}

/* Карточка */
.stat-card {
  background-color: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  border-top: 4px solid var(--primary-color);
  align-items: center;
}

/* На случай, если в HTML остались inline style width:49%/50% */
.statistics .stat-card[style] {
  width: auto !important;
  max-width: none !important;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card h3 {
  color: var(--primary-color);
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
  line-height: 1.4;
}

.stat-value {
  font-size: 42px;
  font-weight: 700;
  color: var(--primary-color);
  margin-top: auto;
}

/* Пустое состояние (у тебя в HTML class="no-results") */
.no-results {
  padding: 60px 20px;
  text-align: center;
  color: var(--primary-color);
}

.no-results h2 {
  margin-bottom: 15px;
  font-size: 24px;
}

.no-results p {
  color: #666;
  font-size: 16px;
}
