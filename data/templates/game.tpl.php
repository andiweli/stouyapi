<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
 <head>
  <meta charset="utf-8"/>
  <title><?= htmlspecialchars($json->title); ?> - OUYA game</title>
  <meta name="generator" content="stouyapi"/>
  <meta name="author" content="<?= htmlspecialchars($json->developer->name) ?>"/>
  <link rel="stylesheet" type="text/css" href="../ouya-game.css"/>
  <link rel="icon" href="../favicon.ico"/>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>"/>
  <?php if (!$blockedInWeb): ?>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta property="og:title" content="<?= htmlspecialchars($json->title); ?>" />
  <meta property="og:description" content="<?= htmlspecialchars(substr(strtok($json->description, '.!'), 0, 200)); ?>." />
  <meta property="og:image" content="<?= htmlspecialchars($json->tileImage); ?>" />
  <?php endif ?>
 </head>
 <body class="game">
  <header>
   <a href="../discover/"><img class="ouyalogo" src="../ouya-logo.grey.svg" alt="OUYA logo" width="50"/></a>
  </header>
  <section class="text">
   <h1><?= htmlspecialchars($json->title); ?></h1>
   <dl class="meta">
    <dt>Rating</dt>
    <dd class="rating">
     <span class="average average-<?= round($json->rating->average) ?>" title="<?= $json->rating->average ?>"><?= $json->rating->average ?></span>
     <span class="count">(<?= $json->rating->count ?>)</span>
    </dd>

    <dt>Developer</dt>
    <dd class="company">
     <?php if ($developerUrl): ?>
      <a href="<?= htmlspecialchars($developerUrl) ?>"><?= htmlspecialchars($json->developer->name) ?></a>
     <?php else: ?>
      <?= htmlspecialchars($json->developer->name) ?>
     <?php endif ?>
    </dd>

    <dt>Suggested age</dt>
    <dd class="contentRating">
     <?= htmlspecialchars($json->suggestedAge) ?>
    </dd>

    <dt>Number of players</dt>
    <dd class="players">
     <?= htmlspecialchars(implode(', ', $json->gamerNumbers)) ?>
    </dd>

    <dt>Download size</dt>
    <dd class="size">
     <?= number_format($json->apk->fileSize / 1024 / 1024, 2) ?> MiB
    </dd>
   </dl>

   <p class="description">
    <?php if ($json->inAppPurchases): ?>
    <strong>* Includes in-app purchases</strong><br/><br/>
    <?php endif ?>
    <?php if ($blockedInWeb): ?>
        <span class="blocked"><?= nl2br(htmlspecialchars($blockedInWebText)) ?></span>
    <?php else: ?>
        <?= nl2br(htmlspecialchars($json->description)) ?>
    <?php endif ?>
   </p>
  </section>

  <?php if (!$blockedInWeb): ?>
  <section class="media">
   <h2>Screenshots</h2>
   <div class="content">
    <?php foreach ($json->mediaTiles as $tile): ?>
     <?php if ($tile->type == 'image'): ?>
      <img src="<?= htmlspecialchars($tile->urls->full) ?>" alt="Screenshot of <?= htmlspecialchars($json->title); ?>"/>
     <?php elseif ($tile->type == 'video'): ?>
      <video controls="">
       <source src="<?= htmlspecialchars($tile->url) ?>"/>
      </video>
     <?php endif ?>
    <?php endforeach ?>
   </div>
  </section>
  <?php endif ?>

  <section class="buttons">
   <h2>Links</h2>
   <?php if (!$blockedInWeb && $apkDownloadUrl): ?>
   <div>
    <a href="<?= $apkDownloadUrl ?>">Download .apk</a>
    <p>
     Version <?= $json->version->number ?>, published
     <?= gmdate('Y-m-d', $json->version->publishedAt) ?>
    </p>
   </div>
   <?php endif ?>
   <?php if (!$blockedInWeb && $internetArchiveUrl): ?>
   <div>
    <a href="<?= $internetArchiveUrl ?>">Internet Archive</a>
   </div>
   <?php endif ?>
   <?php if ($developerDetailsUrl): ?>
   <div>
    <a href="<?= $developerDetailsUrl ?>">Developer page</a>
   </div>
   <?php endif ?>
   <?php if ($appsJson->app->website): ?>
   <div>
    <a href="<?= $appsJson->app->website ?>">Game website</a>
   </div>
   <?php endif ?>
   <div>
    <form method="post" action="<?= htmlspecialchars($pushUrl) ?>" id="push" onsubmit="pushToMyOuya();return false;">
     <button name="push" type="submit" class="push-to-my-ouya">
      <img src="../push-to-my-ouya.png" width="335" height="63"
           alt="Push to my OUYA"
      />
     </button>
    </form>
   </div>
  </section>

  <nav>
   <?php foreach ($navLinks as $url => $title): ?>
    <a rel="up" href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($title) ?></a>
   <?php endforeach ?>
  </nav>

  <div style="display: none" class="popup" id="push-success">
   <a class="close" href="#" onclick="this.parentNode.style.display='none';return false;">⊗</a>
   <strong><?= htmlspecialchars($json->title); ?></strong>
   will start downloading to your OUYA within the next few minutes
  </div>
  <div style="display: none" class="popup" id="push-error">
   <a class="close" href="#" onclick="this.parentNode.style.display='none';return false;">⊗</a>
   <strong>Push error</strong>
   <p>error message</p>
  </div>

  <script type="text/javascript">
   function pushToMyOuya() {
       var form = document.getElementById("push");
       var req = new XMLHttpRequest();
       req.addEventListener("load", pushToMyOuyaComplete);
       req.open("POST", form.action);
       req.send();
   }
   function pushToMyOuyaComplete() {
       if (this.status / 100 == 2) {
           document.getElementById('push-success').style.display = "";
       } else {
           var err = document.getElementById('push-error');
           err.getElementsByTagName("p")[0].textContent = this.responseText;
           err.style.display = "";
       }
   }
  </script>
 </body>
</html>
