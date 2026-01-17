<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Block;
use App\Models\ServiceBlock;
use App\Models\Service;
use App\Helpers\Sanitizer;

/**
 * Service Blocks Controller (AJAX)
 * We're Sinapsis CMS
 */
class ServiceBlocksController extends Controller
{
    private ServiceBlock $blockModel;
    private Service $serviceModel;

    public function __construct()
    {
        parent::__construct();
        $this->blockModel = new ServiceBlock();
        $this->serviceModel = new Service();
    }

    /**
     * Get blocks for a service (AJAX)
     */
    public function index(string $serviceId): void
    {
        $this->requireAuth();

        $blocks = $this->blockModel->getAllForService((int) $serviceId);

        // Decode JSON for each block
        $blocks = array_map(function ($block) {
            $block['content'] = json_decode($block['content'], true) ?? [];
            $block['settings'] = json_decode($block['settings'], true) ?? [];
            return $block;
        }, $blocks);

        $this->json([
            'success' => true,
            'blocks' => $blocks,
        ]);
    }

    /**
     * Store new block (AJAX)
     */
    public function store(string $serviceId): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }

        $service = $this->serviceModel->find((int) $serviceId);

        if (!$service) {
            $this->json(['success' => false, 'error' => 'Service not found'], 404);
            return;
        }

        $blockType = Sanitizer::string($this->getPost('block_type'));

        if (!isset(Block::TYPES[$blockType])) {
            $this->json(['success' => false, 'error' => 'Invalid block type'], 400);
            return;
        }

        // Get content from POST or use defaults
        $content = Sanitizer::json($this->getPost('content'));
        $settings = Sanitizer::json($this->getPost('settings'));

        $blockId = $this->blockModel->createBlock([
            'service_id' => (int) $serviceId,
            'block_type' => $blockType,
            'content' => $content ?? Block::getDefaultContent($blockType),
            'settings' => $settings ?? Block::getDefaultSettings($blockType),
            'is_active' => 1,
        ]);

        $block = $this->blockModel->find($blockId);
        $block['block_type'] = $block['type'];
        $block['content'] = json_decode($block['content'], true) ?? [];
        $block['settings'] = json_decode($block['settings'], true) ?? [];

        $this->json([
            'success' => true,
            'block' => $block,
            'message' => 'Bloque creado correctamente',
        ]);
    }

    /**
     * Get single block (AJAX)
     */
    public function get(string $id): void
    {
        $this->requireAuth();

        $block = $this->blockModel->find((int) $id);

        if (!$block) {
            $this->json(['success' => false, 'error' => 'Block not found'], 404);
            return;
        }

        $block['content'] = json_decode($block['content'], true) ?? [];
        $block['settings'] = json_decode($block['settings'], true) ?? [];

        $this->json([
            'success' => true,
            'block' => $block,
        ]);
    }

    /**
     * Update block (AJAX)
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }

        $block = $this->blockModel->find((int) $id);

        if (!$block) {
            $this->json(['success' => false, 'error' => 'Block not found'], 404);
            return;
        }

        $data = [];

        // Update content if provided
        $content = Sanitizer::json($this->getPost('content'));
        if ($content !== null) {
            $data['content'] = $content;
        }

        // Update settings if provided
        $settings = Sanitizer::json($this->getPost('settings'));
        if ($settings !== null) {
            $data['settings'] = $settings;
        }

        // Update is_active if provided
        if ($this->getPost('is_active') !== null) {
            $data['is_active'] = Sanitizer::bool($this->getPost('is_active')) ? 1 : 0;
        }

        if (empty($data)) {
            $this->json(['success' => false, 'error' => 'No data to update'], 400);
            return;
        }

        $this->blockModel->updateBlock((int) $id, $data);

        $updatedBlock = $this->blockModel->find((int) $id);
        $updatedBlock['block_type'] = $updatedBlock['type'];
        $updatedBlock['content'] = json_decode($updatedBlock['content'], true) ?? [];
        $updatedBlock['settings'] = json_decode($updatedBlock['settings'], true) ?? [];

        $this->json([
            'success' => true,
            'block' => $updatedBlock,
            'message' => 'Bloque actualizado correctamente',
        ]);
    }

    /**
     * Delete block (AJAX)
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }

        $block = $this->blockModel->find((int) $id);

        if (!$block) {
            $this->json(['success' => false, 'error' => 'Block not found'], 404);
            return;
        }

        $this->blockModel->delete((int) $id);

        $this->json([
            'success' => true,
            'message' => 'Bloque eliminado correctamente',
        ]);
    }

    /**
     * Clone block (AJAX)
     */
    public function clone(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }

        $block = $this->blockModel->find((int) $id);

        if (!$block) {
            $this->json(['success' => false, 'error' => 'Block not found'], 404);
            return;
        }

        // Create a copy of the block
        $newBlockId = $this->blockModel->createBlock([
            'service_id' => (int) $block['service_id'],
            'block_type' => $block['type'],
            'content' => json_decode($block['content'], true) ?? [],
            'settings' => json_decode($block['settings'], true) ?? [],
            'is_active' => (int) $block['is_active'],
        ]);

        $newBlock = $this->blockModel->find($newBlockId);
        $newBlock['block_type'] = $newBlock['type'];
        $newBlock['content'] = json_decode($newBlock['content'], true) ?? [];
        $newBlock['settings'] = json_decode($newBlock['settings'], true) ?? [];

        $this->json([
            'success' => true,
            'block' => $newBlock,
            'message' => 'Bloque clonado correctamente',
        ]);
    }

    /**
     * Reorder blocks (AJAX)
     */
    public function reorder(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }

        $serviceId = Sanitizer::int($this->getPost('service_id'));
        $blockIds = $this->getPost('block_ids');

        if (!$serviceId || !is_array($blockIds)) {
            $this->json(['success' => false, 'error' => 'Invalid data'], 400);
            return;
        }

        try {
            $this->blockModel->reorder($serviceId, array_map('intval', $blockIds));

            $this->json([
                'success' => true,
                'message' => 'Orden actualizado correctamente',
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get block form HTML (AJAX)
     */
    public function form(): void
    {
        $this->requireAuth();

        $blockType = Sanitizer::string($this->getQuery('type'));
        $blockId = Sanitizer::int($this->getQuery('block_id'));

        if (!isset(Block::TYPES[$blockType])) {
            $this->json(['success' => false, 'error' => 'Invalid block type'], 400);
            return;
        }

        $block = null;
        if ($blockId) {
            $block = $this->blockModel->find($blockId);
            if ($block) {
                $block['content'] = json_decode($block['content'], true) ?? [];
                $block['settings'] = json_decode($block['settings'], true) ?? [];
            }
        }

        // Render block form
        ob_start();
        $this->renderBlockForm($blockType, $block);
        $html = ob_get_clean();

        $this->json([
            'success' => true,
            'html' => $html,
            'type' => $blockType,
            'typeName' => Block::TYPES[$blockType],
        ]);
    }

    /**
     * Render block form based on type
     */
    private function renderBlockForm(string $type, ?array $block = null): void
    {
        $content = $block['content'] ?? Block::getDefaultContent($type);
        $settings = $block['settings'] ?? Block::getDefaultSettings($type);
        $blockId = $block['id'] ?? null;

        $templatePath = TEMPLATES_PATH . '/admin/blocks/forms/' . $type . '.php';

        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            include TEMPLATES_PATH . '/admin/blocks/forms/default.php';
        }
    }
}
