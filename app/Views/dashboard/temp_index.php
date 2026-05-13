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
