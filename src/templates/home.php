<?php use Vector\Engine\TemplateEngine;
TemplateEngine::get_template_part('header'); 
global $template_data; ?>

<main>
  <h1> <?php echo $template_data['pagename']; ?> </h1>
</main>

<?php TemplateEngine::get_template_part('footer'); ?>
