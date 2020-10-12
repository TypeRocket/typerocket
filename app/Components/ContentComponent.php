<?php
namespace App\Components;

use TypeRocket\Template\Component;

class ContentComponent extends Component
{
    protected $title = 'Content Component';

    /**
     * Admin Fields
     */
    public function fields()
    {
        $form = $this->form();

        echo $form->text('Headline');
        echo $form->image('Featured Image');
        echo $form->textarea('Content');
    }

    /**
     * Render
     *
     * @var array $data component fields
     * @var array $info name, item_id, model, first_item, last_item, component_id, hash
     */
    public function render(array $data, array $info)
    {
        ?>
        <div class="builder-content">
            <h2><?php echo esc_html($data['headline']); ?></h2>
            <?php echo wp_get_attachment_image($data['featured_image']); ?>
            <?php echo wpautop( $data['content'] ); ?>
        </div>
        <?php
    }
}