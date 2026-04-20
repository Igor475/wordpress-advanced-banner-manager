<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap ab-admin-edit">
    <h1><?php echo $banner ? 'Editar Banner' : 'Novo Banner'; ?></h1>

    <div id="ab-banner-message" style="display:none; margin-top:10px;"></div>

    <form id="ab-banner-form">
        <?php wp_nonce_field('ab_nonce', 'nonce'); ?>
        <input type="hidden" name="action" value="ab_save_banner">
        <input type="hidden" id="banner_id" name="banner_id" value="<?php echo $banner->id ?? 0; ?>">

        <div class="ab-form-section">
            <h2>Informações do Banner</h2>
            <table class="form-table">
                <tr>
                    <th><label for="banner_name">Nome</label></th>
                    <td>
                        <input type="text" name="banner_name" id="banner_name"
                            value="<?php echo esc_attr($banner->name ?? ''); ?>" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th><label>Slug</label></th>
                    <td>
                        <input type="text" value="<?php echo esc_attr($banner->slug ?? ''); ?>" class="regular-text" readonly>
                        <p class="description">Gerado automaticamente a partir do nome.</p>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <select name="banner_status">
                            <option value="1" <?php selected($banner->status ?? 1, 1); ?>>Ativo</option>
                            <option value="0" <?php selected($banner->status ?? 1, 0); ?>>Inativo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="banner_full_width">Tela Cheia</label></th>
                    <td>
                        <input type="checkbox" name="banner_full_width" id="banner_full_width" value="1" <?php checked($banner->full_width ?? 0, 1); ?>>
                        <span class="description">Marque se este banner deve ocupar toda a largura do site.</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ab-form-section">
            <h2>Configurações do Slider</h2>
            <table class="form-table">
                <tr>
                    <th><label for="banner_carousel">Ativar Carousel</label></th>
                    <td><input type="checkbox" name="banner_carousel" id="banner_carousel" value="1" <?php checked($banner->carousel ?? 0, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="banner_dots">Exibir Dots</label></th>
                    <td><input type="checkbox" name="banner_dots" id="banner_dots" value="1" <?php checked($banner->dots ?? 1, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="banner_arrows">Exibir Setas</label></th>
                    <td><input type="checkbox" name="banner_arrows" id="banner_arrows" value="1" <?php checked($banner->arrows ?? 1, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="banner_autoplay">Ativar Autoplay</label></th>
                    <td><input type="checkbox" name="banner_autoplay" id="banner_autoplay" value="1" <?php checked($banner->autoplay ?? 1, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="banner_interval">Intervalo do Autoplay (ms)</label></th>
                    <td>
                        <input type="number" name="banner_interval" id="banner_interval"
                            value="<?php echo esc_attr($banner->interval_ms ?? 4000); ?>" min="100" step="100">
                    </td>
                </tr>
            </table>
        </div>

        <div class="ab-items-section">
            <div class="ab-items-toolbar">
                <div>
                    <h2>Itens do Banner</h2>
                    <p class="description">Organize imagens, links e período de exibição de cada item.</p>
                </div>
                <label class="ab-compact-toggle">
                    <input type="checkbox" id="toggle-compact" checked>
                    Visualização compacta
                </label>
            </div>

            <table class="widefat compact" id="ab-items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                    </tr>
                </thead>
                <tbody id="ab-items-list">
                    <?php if ($items): foreach ($items as $index => $it): ?>
                        <tr class="ab-item-row">
                            <td>
                                <div class="ab-item-card">
                                    <div class="ab-item-header">
                                        <div class="ab-item-title-group">
                                            <span class="ab-handle" role="button" tabindex="0" aria-label="Reordenar item">☰</span>
                                            <strong class="ab-item-title">Item <span class="ab-item-index"><?php echo intval($index + 1); ?></span></strong>
                                        </div>
                                        <div class="ab-item-meta">
                                            <span class="ab-stat-pill">Views: <strong><?php echo intval($it->views ?? 0); ?></strong></span>
                                            <span class="ab-stat-pill">Cliques: <strong><?php echo intval($it->clicks ?? 0); ?></strong></span>
                                            <button type="button" class="button button-link-delete ab-remove">Remover item</button>
                                        </div>
                                        <input type="hidden" class="ordering" name="ordering[]" value="<?php echo intval($it->ordering ?? 0); ?>">
                                        <input type="hidden" name="item_id[]" value="<?php echo intval($it->id); ?>">
                                    </div>

                                    <div class="ab-item-grid">
                                        <div class="ab-image-field">
                                            <label class="ab-field-label">Imagem Desktop</label>
                                            <div class="ab-image-panel">
                                                <div class="ab-preview-wrap <?php echo $it->image_id ? 'has-image' : ''; ?>">
                                                    <?php if ($it->image_id): ?>
                                                        <?php echo wp_get_attachment_image($it->image_id, 'medium', false, ['class' => 'ab-preview']); ?>
                                                    <?php else: ?>
                                                        <span class="ab-preview-placeholder">Selecione a imagem desktop</span>
                                                    <?php endif; ?>
                                                </div>
                                                <input type="hidden" name="image_id[]" value="<?php echo intval($it->image_id); ?>">
                                                <div class="ab-image-actions">
                                                    <button type="button" class="button ab-upload">Selecionar</button>
                                                    <button type="button" class="button ab-remove-image">Remover</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ab-image-field">
                                            <label class="ab-field-label">Imagem Mobile</label>
                                            <div class="ab-image-panel">
                                                <div class="ab-preview-wrap <?php echo !empty($it->image_id_mobile) ? 'has-image' : ''; ?>">
                                                    <?php if (!empty($it->image_id_mobile)): ?>
                                                        <?php echo wp_get_attachment_image($it->image_id_mobile, 'medium', false, ['class' => 'ab-preview-mobile']); ?>
                                                    <?php else: ?>
                                                        <span class="ab-preview-placeholder">Selecione a imagem mobile</span>
                                                    <?php endif; ?>
                                                </div>
                                                <input type="hidden" name="image_id_mobile[]" value="<?php echo intval($it->image_id_mobile ?? 0); ?>">
                                                <div class="ab-image-actions">
                                                    <button type="button" class="button ab-upload-mobile">Selecionar</button>
                                                    <button type="button" class="button ab-remove-image-mobile">Remover</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ab-field ab-field-full">
                                            <label class="ab-field-label">Link de destino</label>
                                            <input type="url" name="url[]" value="<?php echo esc_url($it->url); ?>" placeholder="https://exemplo.com/produto/">
                                        </div>

                                        <div class="ab-field-group">
                                            <label class="ab-field-label">Início da exibição</label>
                                            <div class="ab-inline-fields">
                                                <input type="text" class="ab-date" name="start_date[]" value="<?php echo esc_attr($it->start_date); ?>" placeholder="YYYY-MM-DD">
                                                <input type="text" class="ab-time" name="start_time[]" value="<?php echo esc_attr($it->start_time); ?>" placeholder="HH:MM:SS">
                                            </div>
                                        </div>

                                        <div class="ab-field-group">
                                            <label class="ab-field-label">Fim da exibição</label>
                                            <div class="ab-inline-fields">
                                                <input type="text" class="ab-date" name="end_date[]" value="<?php echo esc_attr($it->end_date); ?>" placeholder="YYYY-MM-DD">
                                                <input type="text" class="ab-time" name="end_time[]" value="<?php echo esc_attr($it->end_time); ?>" placeholder="HH:MM:SS">
                                            </div>
                                        </div>

                                        <div class="ab-field-group ab-status-field">
                                            <label class="ab-field-label">Status</label>
                                            <select name="item_status[]">
                                                <option value="1" <?php selected($it->status, 1); ?>>Ativo</option>
                                                <option value="0" <?php selected($it->status, 0); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <button id="ab-add-item" class="button button-secondary">Adicionar Item</button>
        </div>

        <p class="submit">
            <button class="button-primary">Salvar</button>
            <a href="<?php echo admin_url('admin.php?page=ab_banners'); ?>" class="button">Cancelar</a>
        </p>
    </form>
</div>
