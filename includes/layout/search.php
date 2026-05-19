<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
    <div class="col-md-12 pt-3 pb-3">
    </div>
  </div>
</section>
<section class="probootstrap-section probootstrap-bg-white">
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1 probootstrap-animate">
        <div class="probootstrap-heading dark text-center">
          <h1 class="primary-heading">Search</h1>
          <h3 class="secondary-heading"><?php echo ($searchQuery !== '') ? htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') : 'Find products and information'; ?></h3>
        </div>

        <form method="get" action="/search" class="search-results-form">
          <div class="row">
            <div class="col-sm-9">
              <label class="sr-only" for="search-page-query">Search</label>
              <input type="search" id="search-page-query" name="q" class="form-control input-lg" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search for products, delivery information, FAQs..." />
            </div>
            <div class="col-sm-3">
              <button type="submit" class="btn btn-warning btn-lg btn-block">Search</button>
            </div>
          </div>
        </form>

        <div class="search-results-list">
          <?php if ($searchQuery === ''): ?>
            <p>Start typing above to search for products, delivery information, FAQs, and pages across the site.</p>
          <?php elseif (empty($searchResults)): ?>
            <p class="search-results-summary">No results found for <strong><?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
          <?php else: ?>
            <p class="search-results-summary"><?php echo count($searchResults); ?> result<?php echo (count($searchResults) === 1) ? '' : 's'; ?> found for <strong><?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
            <?php foreach ($searchResults as $result): ?>
              <article class="search-result-card">
                <div class="search-result-card__inner">
                  <?php if (isset($result['image']) && strlen($result['image']) > 0): ?>
                    <a class="search-result-card__image" href="<?php echo htmlspecialchars($result['url'], ENT_QUOTES, 'UTF-8'); ?>">
                      <img src="<?php echo htmlspecialchars($result['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    </a>
                  <?php endif; ?>
                  <div class="search-result-card__content">
                    <p class="search-result-type"><?php echo htmlspecialchars($result['type'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <h3><a href="<?php echo htmlspecialchars($result['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                    <?php if (strlen($result['excerpt']) > 0): ?>
                      <p><?php echo htmlspecialchars($result['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .search-results-form {
    margin-bottom: 30px;
  }

  .search-results-list {
    margin-top: 40px;
    text-align: left !important;
  }

  .search-results-summary {
    margin-bottom: 24px;
    text-align: left !important;
    padding-top: 30px;
  }

  .search-results-list p,
  .search-results-list h3,
  .search-result-card,
  .search-result-card__content {
    text-align: left !important;
  }

  .search-result-card {
    padding: 22px 0;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
  }

  .search-result-card__inner {
    display: flex;
    align-items: flex-start;
    gap: 22px;
  }

  .search-result-card__image {
    flex: 0 0 140px;
    display: block;
  }

  .search-result-card__image img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 14px;
    display: block;
  }

  .search-result-card__content {
    flex: 1 1 auto;
    min-width: 0;
  }

  .search-result-card:last-child {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  }

  .search-result-type {
    margin-bottom: 8px;
    color: #b51f29;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
  }

  .search-result-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
  }

  @media (max-width: 767px) {
    .search-results-form .col-sm-3 {
      margin-top: 12px;
    }

    .search-result-card__inner {
      display: block;
    }

    .search-result-card__image {
      margin-bottom: 16px;
    }

    .search-result-card__image img {
      width: 100%;
      max-width: 220px;
      height: auto;
      aspect-ratio: 1 / 1;
    }
  }
</style>
