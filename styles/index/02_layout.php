.content-area {
    flex: 1;
}


 .content-area .container {
        padding: 15px 0;
}


.main-wrapper {
  display: flex;
  align-items: flex-start;
  gap: 20px;

  /* вместо media padding */
  padding: 0 15px;

  /* вместо media flex-direction */
  flex-wrap: wrap;
}

/* если есть левое меню — держим его фиксированным */
#nav-left, .nav-left, .sidebar {
  flex: 0 0 260px;
  min-width: 260px;
}

/* контент пусть забирает остаток и умеет сжиматься */
.content-area {
  flex: 1 1 600px;
  min-width: 0;
}

h1 {
  color: var(--primary-color);
  font-weight: 600;
  font-size: 24px;
  margin-bottom: 10px;
}

.subtitle {
  color: #666;
  font-size: 14px;
  margin-bottom: 20px;
}

/* Скроллбар */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--primary-hover);
}