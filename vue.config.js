module.exports = {
  // proxy API requests to Valet during development
  devServer: {
    proxy: 'https://neft.com'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: 'public',
  
  pages: {
    index: {
      // точка входа для страницы
      entry: 'resources/assets/main.js',
      // исходный шаблон
      template: 'resources/assets/index.html',
      // в результате будет dist/index.html
      filename: '../resources/views/index.blade.php',
      // когда используется опция title, то <title> в шаблоне
      // должен быть <title><%= htmlWebpackPlugin.options.title %></title>
      title: 'Blank',
      // все фрагменты, добавляемые на этой странице, по умолчанию
      // это извлечённые общий фрагмент и вендорный фрагмент.
      chunks: ['chunk-vendors', 'chunk-common', 'index']
    },
  }
}
