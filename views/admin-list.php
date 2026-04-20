<?php if (!defined('ABSPATH'))
    exit; ?>
<div class="wrap">
    <h1 class="wp-heading-inline">Banners</h1>
    <a href="<?php echo admin_url('admin.php?page=ab_banners_new'); ?>" class="page-title-action">Adicionar Novo</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped banners-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Shortcode</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($banners):
                foreach ($banners as $b): ?>
                    <tr>
                        <td class="column-primary">
                            <strong>
                                <a class="row-title" href="<?php echo admin_url('admin.php?page=ab_banners_new&edit=' . $b->id); ?>">
                                    <?php echo esc_html($b->name); ?>
                                </a>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo admin_url('admin.php?page=ab_banners_new&edit=' . $b->id); ?>">Editar</a> |
                                </span>
                                <span class="delete">
                                    <a href="#" class="ab-delete" data-id="<?php echo $b->id; ?>">Excluir</a> |
                                </span>
                                <span class="duplicate">
                                    <a href="#" class="ab-duplicate" data-id="<?php echo $b->id; ?>">Duplicar</a>
                                </span>
                            </div>
                        </td>
                        <td><?php echo esc_html($b->slug); ?></td>
                        <td><?php echo $b->status ? 'Ativo' : 'Inativo'; ?></td>
                        <td>
                            <div>
                                <strong>Por ID:</strong><br>
                                <code>[banners id="<?php echo intval($b->id); ?>"]</code>
                            </div>
                            <div style="margin-top:5px;">
                                <strong>Por Slug:</strong><br>
                                <code>[banners slug="<?php echo esc_attr($b->slug); ?>"]</code>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5">Nenhum banner cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>