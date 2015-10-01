<div class="form-wrapper">
    <div class="form-label">
        <label class="optional"><?php echo $this->data['label']?></label>
    </div>
    <div class="form-element">
        <p class="description">
            <?php echo $this->data['description']?>
        </p>
        <?php
            $elements = $this->element->getElements();
            foreach ($elements as $element) {
                echo '<div>' . $element . '</div>';
            }
        ?>
    </div>
</div>

