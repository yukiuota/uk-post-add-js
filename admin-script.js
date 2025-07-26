jQuery(document).ready(function ($) {

    // textarea の高さを自動調整
    $('#uk_custom_js').on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // シンタックスハイライト風の基本的な機能
    $('#uk_custom_js').on('keydown', function (e) {
        // Tabキーでインデント
        if (e.keyCode === 9) {
            e.preventDefault();
            var start = this.selectionStart;
            var end = this.selectionEnd;
            var value = this.value;

            this.value = value.substring(0, start) + '    ' + value.substring(end);
            this.selectionStart = this.selectionEnd = start + 4;
        }
    });

    // 保存前の確認機能
    var originalContent = $('#uk_custom_js').val();

    $('#post').on('submit', function () {
        var currentContent = $('#uk_custom_js').val();

        if (currentContent !== originalContent && currentContent.trim() !== '') {
            // scriptタグを含む場合の基本的なチェック
            var scriptRegex = /<script[^>]*>(.*?)<\/script>/gi;
            var match;
            var hasError = false;
            var errorMessage = '';

            // scriptタグ内のJavaScriptコードのみを検証
            while ((match = scriptRegex.exec(currentContent)) !== null) {
                var scriptContent = match[1];
                if (scriptContent.trim() !== '') {
                    try {
                        // 関数として評価してみる（実行はしない）
                        new Function(scriptContent);
                    } catch (e) {
                        hasError = true;
                        errorMessage = e.message;
                        break;
                    }
                }
            }

            if (hasError) {
                var confirmed = confirm('入力されたJavaScriptに構文エラーがある可能性があります：\n\n' + errorMessage + '\n\nそれでも保存しますか？');
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            }
        }

        return true;
    });

    // プレビュー機能（安全のため無効化）
    $('.uk-js-preview').on('click', function (e) {
        e.preventDefault();
        alert('セキュリティ上の理由により、プレビュー機能は無効化されています。変更を保存してフロントエンドで確認してください。');
    });
});