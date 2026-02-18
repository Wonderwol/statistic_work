<?php
declare(strict_types=1);
include __DIR__ . '/../chapter1/style.php';

?>

<style>
  /* ===== Chapter 3: line layout ===== */
  .line-layout{
    display: grid;
    grid-template-columns: 1fr 180px;
    gap: 16px;
    align-items: start;
  }

  .chart-wrap--line{
    height: 320px;
    min-height: 280px;
  }

  .line-legend{
    border: 1px solid rgba(109,68,75,0.35);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
  }

  .line-legend__item{
    width: 100%;
    display: block;
    text-align: center;
    padding: 7px 8px;
    border: 0;
    border-bottom: 1px solid rgba(109,68,75,0.25);
    background: #fff;
    color: rgba(44,62,80,0.92);
    font-weight: 700;
    cursor: pointer;
    user-select: none;
  }
  .line-legend__item:last-child{ border-bottom: 0; }

  .line-legend__item:hover{
    background: rgba(109,68,75,0.07);
  }

  .line-legend__item.is-active{
    background: var(--primary-color);
    color: #fff;
  }

  /* Адаптив */
  @media (max-width: 920px){
    .line-layout{ grid-template-columns: 1fr; }
    .line-legend{ display: grid; grid-template-columns: repeat(3, 1fr); }
    .line-legend__item{ border-bottom: 0; border-right: 1px solid rgba(109,68,75,0.25); }
    .line-legend__item:nth-child(3n){ border-right: 0; }
  }
  @media (max-width: 520px){
    .line-legend{ grid-template-columns: repeat(2, 1fr); }
    .line-legend__item{ border-right: 1px solid rgba(109,68,75,0.25); }
    .line-legend__item:nth-child(2n){ border-right: 0; }
  }

    /* Info-иконка как в других разделах: без рамок/фона, нормальный размер */
  .info-link.info-link--circle{
    width: 28px;
    height: 28px;
    padding: 0;
    margin: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50% !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    text-decoration: none;
    flex: 0 0 auto;
  }
  .info-link.info-link--circle:hover{
    background: rgba(0,0,0,0.06) !important;
  }
  .info-link.info-link--circle img{
    width: 28px;
    height: 28px;
    display: block;
  }

</style>
