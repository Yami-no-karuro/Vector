<?php use Vector\Engine\TemplateEngine;
TemplateEngine::get_template_part('header'); ?>

<main>
  <h1> <?php echo self::$template_data['pagename']; ?> </h1>
</main>

<?php TemplateEngine::get_template_part('footer'); ?>
