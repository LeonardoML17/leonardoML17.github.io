<?php
/**
 * Plantilla para mostrar propiedades Wasi
 * 
 * @package Wasi API
 * @version 3.1
 */
?>
<div class="wasi-properties">
    <?php foreach ($properties as $prop): ?>
        <div class="wasi-property">
            <?php if(!empty($prop['main_image']['id_image'])): ?>
                <div class="property-image">
                    <img src="https://api.wasi.co/img.php?id=<?php echo esc_attr($prop['main_image']['id_image']); ?>&w=800&h=600&q=85" 
                         alt="<?php echo esc_attr($prop['title'] ?? __('Propiedad', 'wasi-api')); ?>" 
                         loading="lazy">
                </div>
            <?php endif; ?>

            <div class="property-details">
                <h3 class="property-title"><?php echo esc_html($prop['title']); ?></h3>
                <p class="property-price">
                    <strong><?php _e('Precio:', 'wasi-api'); ?></strong> 
                    $<?php echo number_format($prop['price'], 2); ?>
                </p>
                <p class="property-type">
                    <strong><?php _e('Tipo:', 'wasi-api'); ?></strong> 
                    <?php echo esc_html($prop['type']); ?>
                </p>

                <?php if(!empty($prop['description'])): ?>
                    <div class="property-description">
                        <?php echo wp_kses_post(wpautop($prop['description'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>