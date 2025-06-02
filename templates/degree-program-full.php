<?php
//var_dump($data);
?>

<section class="fau-studium-display degree-program-full" itemtype="https://schema.org/EducationalOccupationalProgram" itemscope>
    <?php echo $data['featured_image']['rendered']; ?>
    <h1 class="title" itemprop="name">
        <?php echo esc_attr($data['title'] . ' (' . $data['degree']['abbreviation'] . ')'); ?>
    </h1>
    <p class="degree-subtitle"><?php echo esc_attr($data['subtitle']); ?></p>

    <div class="description">
        <?php echo $data['content']['about']['description']; ?>
    </div>


</section>
