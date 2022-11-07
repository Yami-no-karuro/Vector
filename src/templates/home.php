<?php use Vector\Engine\TemplateEngine;
self::get_template_part('header'); ?>

<main>
  <h1> <?php echo self::$template_data['pagename']; ?> </h1>
</main>

<?php self::get_template_part('footer'); ?>
