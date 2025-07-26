# UK Post Add JS Plugin

WordPressの投稿、固定ページ、カスタム投稿の編集画面にJavaScript入力フィールドを追加し、フロントエンドで出力するプラグインです。

## 機能

- 投稿、固定ページ、カスタム投稿の編集画面にカスタムJavaScriptフィールドを追加
- 入力されたJavaScriptコードをフロントエンドの`wp_footer()`で出力
- シンプルな構文チェック機能
- コード入力時の基本的なエディタ機能（タブインデント等）

## インストール

1. このフォルダ全体を `/wp-content/plugins/` ディレクトリにアップロード
2. WordPress管理画面の「プラグイン」メニューから「UK Add JS」を有効化

## 使用方法

1. 投稿、固定ページ、またはカスタム投稿の編集画面を開く
2. ページ下部に表示される「カスタムJavaScript」メタボックスを探す
3. テキストエリアにJavaScriptコードを入力（`<script>`タグも含めて入力）
4. 投稿を保存
5. フロントエンドでそのページを表示すると、入力したJavaScriptが実行される

## 注意事項

- 入力されたJavaScriptは該当するページでのみ実行されます
- セキュリティのため、管理者権限を持つユーザーのみが編集可能です
- JavaScriptコードは慎重に入力してください（サイトの動作に影響する可能性があります）
- `<script>`タグを含めて入力してください
- 外部CDNからのライブラリ読み込みも可能です

## 出力される場所

入力されたJavaScript（`<script>`タグを含む）は、そのままフロントエンドの`wp_footer`フック（最後の方）で出力されます：

```html
<!-- UK Add JS Plugin Custom JavaScript -->
<script src="https://cdn.example.com/library.js"></script>
<script type="text/javascript">
// ここに入力されたJavaScriptコード
console.log('Hello World');
</script>
<!-- /UK Add JS Plugin Custom JavaScript -->
```

## バージョン

- 1.0.0 - 初回リリース

## 作者

Y.U.

## ライセンス

GPL v2
