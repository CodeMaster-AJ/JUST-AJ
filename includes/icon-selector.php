<div class="form-group">
    <label for="icon">Icon</label>
    <div class="icon-selector">
        <div class="icon-preview" id="iconPreview">
            <i class="fa-solid fa-wrench"></i>
        </div>
        <select id="icon" name="icon" class="icon-select">
            <option value="">Select an icon</option>
            <optgroup label="File & Document">
                <option value="fa-file-pdf" <?php echo ($tool['icon'] ?? '') === 'fa-file-pdf' ? 'selected' : ''; ?>>PDF</option>
                <option value="fa-file-word" <?php echo ($tool['icon'] ?? '') === 'fa-file-word' ? 'selected' : ''; ?>>Word</option>
                <option value="fa-file-excel" <?php echo ($tool['icon'] ?? '') === 'fa-file-excel' ? 'selected' : ''; ?>>Excel</option>
                <option value="fa-file-powerpoint" <?php echo ($tool['icon'] ?? '') === 'fa-file-powerpoint' ? 'selected' : ''; ?>>PowerPoint</option>
                <option value="fa-file-image" <?php echo ($tool['icon'] ?? '') === 'fa-file-image' ? 'selected' : ''; ?>>Image File</option>
                <option value="fa-file-video" <?php echo ($tool['icon'] ?? '') === 'fa-file-video' ? 'selected' : ''; ?>>Video File</option>
                <option value="fa-file-audio" <?php echo ($tool['icon'] ?? '') === 'fa-file-audio' ? 'selected' : ''; ?>>Audio File</option>
                <option value="fa-file-code" <?php echo ($tool['icon'] ?? '') === 'fa-file-code' ? 'selected' : ''; ?>>Code File</option>
                <option value="fa-file-alt" <?php echo ($tool['icon'] ?? '') === 'fa-file-alt' ? 'selected' : ''; ?>>Text File</option>
                <option value="fa-file" <?php echo ($tool['icon'] ?? '') === 'fa-file' ? 'selected' : ''; ?>>File</option>
            </optgroup>
            <optgroup label="Images & Media">
                <option value="fa-image" <?php echo ($tool['icon'] ?? '') === 'fa-image' ? 'selected' : ''; ?>>Image</option>
                <option value="fa-photo-video" <?php echo ($tool['icon'] ?? '') === 'fa-photo-video' ? 'selected' : ''; ?>>Photo/Video</option>
                <option value="fa-portrait" <?php echo ($tool['icon'] ?? '') === 'fa-portrait' ? 'selected' : ''; ?>>Portrait</option>
                <option value="fa-icons" <?php echo ($tool['icon'] ?? '') === 'fa-icons' ? 'selected' : ''; ?>>Icons</option>
                <option value="fa-image-polaroid" <?php echo ($tool['icon'] ?? '') === 'fa-image-polaroid' ? 'selected' : ''; ?>>Polaroid</option>
            </optgroup>
            <optgroup label="PDF Tools">
                <option value="fa-file-pdf" <?php echo ($tool['icon'] ?? '') === 'fa-file-pdf' ? 'selected' : ''; ?>>PDF</option>
                <option value="fa-compress" <?php echo ($tool['icon'] ?? '') === 'fa-compress' ? 'selected' : ''; ?>>Compress</option>
                <option value="fa-expand" <?php echo ($tool['icon'] ?? '') === 'fa-expand' ? 'selected' : ''; ?>>Expand</option>
                <option value="fa-copy" <?php echo ($tool['icon'] ?? '') === 'fa-copy' ? 'selected' : ''; ?>>Copy</option>
                <option value="fa-scissors" <?php echo ($tool['icon'] ?? '') === 'fa-scissors' ? 'selected' : ''; ?>>Cut/Split</option>
                <option value="fa-object-group" <?php echo ($tool['icon'] ?? '') === 'fa-object-group' ? 'selected' : ''; ?>>Merge</option>
            </optgroup>
            <optgroup label="Images & Design">
                <option value="fa-crop" <?php echo ($tool['icon'] ?? '') === 'fa-crop' ? 'selected' : ''; ?>>Crop</option>
                <option value="fa-crop-alt" <?php echo ($tool['icon'] ?? '') === 'fa-crop-alt' ? 'selected' : ''; ?>>Crop Alt</option>
                <option value="fa-magic" <?php echo ($tool['icon'] ?? '') === 'fa-magic' ? 'selected' : ''; ?>>Magic</option>
                <option value="fa-paint-brush" <?php echo ($tool['icon'] ?? '') === 'fa-paint-brush' ? 'selected' : ''; ?>>Paint Brush</option>
                <option value="fa-palette" <?php echo ($tool['icon'] ?? '') === 'fa-palette' ? 'selected' : ''; ?>>Palette</option>
                <option value="fa-drafting-compass" <?php echo ($tool['icon'] ?? '') === 'fa-drafting-compass' ? 'selected' : ''; ?>>Design</option>
                <option value="fa-eraser" <?php echo ($tool['icon'] ?? '') === 'fa-eraser' ? 'selected' : ''; ?>>Eraser</option>
                <option value="fa-vector-square" <?php echo ($tool['icon'] ?? '') === 'fa-vector-square' ? 'selected' : ''; ?>>Vector</option>
            </optgroup>
            <optgroup label="SEO & Content">
                <option value="fa-search" <?php echo ($tool['icon'] ?? '') === 'fa-search' ? 'selected' : ''; ?>>Search</option>
                <option value="fa-search-plus" <?php echo ($tool['icon'] ?? '') === 'fa-search-plus' ? 'selected' : ''; ?>>Search Plus</option>
                <option value="fa-globe" <?php echo ($tool['icon'] ?? '') === 'fa-globe' ? 'selected' : ''; ?>>Globe</option>
                <option value="fa-chart-line" <?php echo ($tool['icon'] ?? '') === 'fa-chart-line' ? 'selected' : ''; ?>>Analytics</option>
                <option value="fa-chart-bar" <?php echo ($tool['icon'] ?? '') === 'fa-chart-bar' ? 'selected' : ''; ?>>Chart</option>
                <option value="fa-key" <?php echo ($tool['icon'] ?? '') === 'fa-key' ? 'selected' : ''; ?>>Key</option>
                <option value="fa-shield-alt" <?php echo ($tool['icon'] ?? '') === 'fa-shield-alt' ? 'selected' : ''; ?>>Shield</option>
                <option value="fa-spell-check" <?php echo ($tool['icon'] ?? '') === 'fa-spell-check' ? 'selected' : ''; ?>>Spell Check</option>
                <option value="fa-language" <?php echo ($tool['icon'] ?? '') === 'fa-language' ? 'selected' : ''; ?>>Language</option>
                <option value="fa-text-width" <?php echo ($tool['icon'] ?? '') === 'fa-text-width' ? 'selected' : ''; ?>>Text Width</option>
            </optgroup>
            <optgroup label="QR & Barcode">
                <option value="fa-qrcode" <?php echo ($tool['icon'] ?? '') === 'fa-qrcode' ? 'selected' : ''; ?>>QR Code</option>
                <option value="fa-barcode" <?php echo ($tool['icon'] ?? '') === 'fa-barcode' ? 'selected' : ''; ?>>Barcode</option>
                <option value="fa-qrcode" <?php echo ($tool['icon'] ?? '') === 'fa-qrcode' ? 'selected' : ''; ?>>QR</option>
                <option value="fa-qrcode" <?php echo ($tool['icon'] ?? '') === 'fa-qrcode' ? 'selected' : ''; ?>>Code</option>
            </optgroup>
            <optgroup label="Tools & Utility">
                <option value="fa-tools" <?php echo ($tool['icon'] ?? '') === 'fa-tools' ? 'selected' : ''; ?>>Tools</option>
                <option value="fa-wrench" <?php echo ($tool['icon'] ?? '') === 'fa-wrench' ? 'selected' : ''; ?>>Wrench</option>
                <option value="fa-wrench-screwdriver" <?php echo ($tool['icon'] ?? '') === 'fa-wrench-screwdriver' ? 'selected' : ''; ?>>Wrench/Screwdriver</option>
                <option value="fa-screwdriver-wrench" <?php echo ($tool['icon'] ?? '') === 'fa-screwdriver-wrench' ? 'selected' : ''; ?>>Screwdriver</option>
                <option value="fa-hammer" <?php echo ($tool['icon'] ?? '') === 'fa-hammer' ? 'selected' : ''; ?>>Hammer</option>
                <option value="fa-screwdriver" <?php echo ($tool['icon'] ?? '') === 'fa-screwdriver' ? 'selected' : ''; ?>>Screwdriver</option>
                <option value="fa-gear" <?php echo ($tool['icon'] ?? '') === 'fa-gear' ? 'selected' : ''; ?>>Gear</option>
                <option value="fa-sliders" <?php echo ($tool['icon'] ?? '') === 'fa-sliders' ? 'selected' : ''; ?>>Sliders</option>
                <option value="fa-sliders-h" <?php echo ($tool['icon'] ?? '') === 'fa-sliders-h' ? 'selected' : ''; ?>>Sliders H</option>
                <option value="fa-settings" <?php echo ($tool['icon'] ?? '') === 'fa-settings' ? 'selected' : ''; ?>>Settings</option>
            </optgroup>
            <optgroup label="Download & Upload">
                <option value="fa-download" <?php echo ($tool['icon'] ?? '') === 'fa-download' ? 'selected' : ''; ?>>Download</option>
                <option value="fa-upload" <?php echo ($tool['icon'] ?? '') === 'fa-upload' ? 'selected' : ''; ?>>Upload</option>
                <option value="fa-cloud-arrow-down" <?php echo ($tool['icon'] ?? '') === 'fa-cloud-arrow-down' ? 'selected' : ''; ?>>Cloud Download</option>
                <option value="fa-cloud-arrow-up" <?php echo ($tool['icon'] ?? '') === 'fa-cloud-arrow-up' ? 'selected' : ''; ?>>Cloud Upload</option>
            </optgroup>
            <optgroup label="Converter">
                <option value="fa-right-left" <?php echo ($tool['icon'] ?? '') === 'fa-right-left' ? 'selected' : ''; ?>>Exchange</option>
                <option value="fa-arrows-rotate" <?php echo ($tool['icon'] ?? '') === 'fa-arrows-rotate' ? 'selected' : ''; ?>>Rotate</option>
                <option value="fa-right-right" <?php echo ($tool['icon'] ?? '') === 'fa-right-right' ? 'selected' : ''; ?>>Arrow Right</option>
                <option value="fa-left-right" <?php echo ($tool['icon'] ?? '') === 'fa-left-right' ? 'selected' : ''; ?>>Arrow Left</option>
                <option value="fa-repeat" <?php echo ($tool['icon'] ?? '') === 'fa-repeat' ? 'selected' : ''; ?>>Repeat</option>
            </optgroup>
            <optgroup label="Office & Productivity">
                <option value="fa-calculator" <?php echo ($tool['icon'] ?? '') === 'fa-calculator' ? 'selected' : ''; ?>>Calculator</option>
                <option value="fa-ruler" <?php echo ($tool['icon'] ?? '') === 'fa-ruler' ? 'selected' : ''; ?>>Ruler</option>
                <option value="fa-ruler-combined" <?php echo ($tool['icon'] ?? '') === 'fa-ruler-combined' ? 'selected' : ''; ?>>Ruler Combined</option>
                <option value="fa-table" <?php echo ($tool['icon'] ?? '') === 'fa-table' ? 'selected' : ''; ?>>Table</option>
                <option value="fa-table-cells" <?php echo ($tool['icon'] ?? '') === 'fa-table-cells' ? 'selected' : ''; ?>>Table Cells</option>
                <option value="fa-clipboard-list" <?php echo ($tool['icon'] ?? '') === 'fa-clipboard-list' ? 'selected' : ''; ?>>Clipboard</option>
                <option value="fa-note-sticky" <?php echo ($tool['icon'] ?? '') === 'fa-note-sticky' ? 'selected' : ''; ?>>Sticky Note</option>
                <option value="fa-list-check" <?php echo ($tool['icon'] ?? '') === 'fa-list-check' ? 'selected' : ''; ?>>Checklist</option>
            </optgroup>
            <optgroup label="General">
                <option value="fa-star" <?php echo ($tool['icon'] ?? '') === 'fa-star' ? 'selected' : ''; ?>>Star</option>
                <option value="fa-heart" <?php echo ($tool['icon'] ?? '') === 'fa-heart' ? 'selected' : ''; ?>>Heart</option>
                <option value="fa-bookmark" <?php echo ($tool['icon'] ?? '') === 'fa-bookmark' ? 'selected' : ''; ?>>Bookmark</option>
                <option value="fa-link" <?php echo ($tool['icon'] ?? '') === 'fa-link' ? 'selected' : ''; ?>>Link</option>
                <option value="fa-external-link" <?php echo ($tool['icon'] ?? '') === 'fa-external-link' ? 'selected' : ''; ?>>External Link</option>
                <option value="fa-share" <?php echo ($tool['icon'] ?? '') === 'fa-share' ? 'selected' : ''; ?>>Share</option>
                <option value="fa-share-alt" <?php echo ($tool['icon'] ?? '') === 'fa-share-alt' ? 'selected' : ''; ?>>Share Alt</option>
                <option value="fa-print" <?php echo ($tool['icon'] ?? '') === 'fa-print' ? 'selected' : ''; ?>>Print</option>
                <option value="fa-save" <?php echo ($tool['icon'] ?? '') === 'fa-save' ? 'selected' : ''; ?>>Save</option>
            </optgroup>
        </select>
    </div>
    <span class="form-hint">Choose an icon from FontAwesome</span>
</div>

<style>
.icon-selector {
    display: flex;
    gap: var(--spacing-3);
    align-items: stretch;
}

.icon-preview {
    width: 56px;
    min-width: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-700);
    border: 1px solid var(--color-gray-600);
    border-radius: var(--border-radius);
    color: var(--color-white);
    font-size: 24px;
}

.icon-select {
    flex: 1;
    padding: var(--spacing-3) var(--spacing-4);
    font-family: var(--font-family);
    font-size: var(--font-size-sm);
    color: var(--color-white);
    background-color: var(--color-gray-800);
    border: 1px solid var(--color-gray-700);
    border-radius: var(--border-radius);
    cursor: pointer;
}

.icon-select:focus {
    outline: none;
    border-color: var(--color-white);
}

.icon-select option {
    padding: var(--spacing-2);
}

.icon-select optgroup {
    font-weight: 600;
    color: var(--color-gray-400);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconSelect = document.getElementById('icon');
    const iconPreview = document.getElementById('iconPreview');
    
    function updateIconPreview() {
        const selectedValue = iconSelect.value;
        if (selectedValue) {
            iconPreview.innerHTML = '<i class="fa-solid ' + selectedValue + '"></i>';
        } else {
            iconPreview.innerHTML = '<i class="fa-solid fa-wrench"></i>';
        }
    }
    
    iconSelect.addEventListener('change', updateIconPreview);
    updateIconPreview();
});
</script>