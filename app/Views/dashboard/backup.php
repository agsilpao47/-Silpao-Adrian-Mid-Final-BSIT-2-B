<!-- Inventory Summary -->
    <div class="dashboard-card">
        <h2>Inventory Summary</h2>
        <div class="inventory-summary">
            <?php if (!empty($inventorySummary)): ?>
                <?php foreach ($inventorySummary as $summary): ?>
                    <div class="summary-item">
                        <div class="summary-category">
                            <span class="category-badge <?= esc(strtolower((string) ($summary->category ?? ''))) ?>"><?= esc((string) ($summary->category ?? '')) ?></span>
                        </div>
                        <div class="summary-details">
                            <div class="detail-item">
                                <span class="detail-label">Items:</span>
                                <span class="detail-value"><?= (int) $summary->count ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Quantity:</span>
                                <span class="detail-value"><?= (int) $summary->total_qty ?></span>
                                <?php if (isset($summary->total_qty) && $summary->total_qty > 0): ?>
                                    <span class="unit-display"><?= esc($summary->category === 'LPG' ? 'kg' : 'pcs') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No inventory data available</p>
            <?php endif; ?>
        </div>
    </div>
