// 与原站 uni-app 行为对齐：全站尺寸按 375 设计稿宽度等比缩放（px -> vw）
module.exports = {
  plugins: {
    'postcss-px-to-viewport-8-plugin': {
      viewportWidth: 375,      // 设计稿宽度
      unitPrecision: 5,
      minPixelValue: 1,        // >=1px 才转换
      mediaQuery: false,
      exclude: [/node_modules/]
    }
  }
}
