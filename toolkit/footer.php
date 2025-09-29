<?php

namespace Toolkit; ?>

</main>

<footer class="footer">
  <?php wp_nav_menu([
      "theme_location" => "footer_menu",
      "container" => false,
  ]); ?>
</footer>
<?php wp_footer(); ?>
</body>

</html>