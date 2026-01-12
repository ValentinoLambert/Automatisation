module.exports = {
  root: true,
  env: { browser: true, es2020: true },
  extends: [
    'airbnb',
    'airbnb/hooks',
    'plugin:react/jsx-runtime',
  ],
  ignorePatterns: ['dist', '.eslintrc.cjs', 'vite.config.js', 'tailwind.config.js', 'postcss.config.js'],
  parserOptions: { ecmaVersion: 'latest', sourceType: 'module' },
  settings: { react: { version: '18.2' } },
  plugins: ['react-refresh'],
  rules: {
    'react-refresh/only-export-components': [
      'warn',
      { allowConstantExport: true },
    ],
    'indent': ['error', 4],
    'react/jsx-indent': ['error', 4],
    'react/jsx-indent-props': ['error', 4],
    'react/jsx-filename-extension': [1, { 'extensions': ['.js', '.jsx'] }],
    'import/no-extraneous-dependencies': ['error', { 'devDependencies': true }],
    'import/prefer-default-export': 'off',
    'no-else-return': 'error',
    'react/function-component-definition': 'off',
    'react/prop-types': 'off', // Often redundant with modern React/TS, or strict prop-types might be too much for this level, but TD didn't specify. Keeping it simple.
    // Actually, TD requirements are specific. I should stick to them and not add too many personal preferences, but airbnb is strict.
    // 'react/react-in-jsx-scope': 'off', // handled by preset
    'jsx-a11y/label-has-associated-control': [
      'error',
      {
        required: {
          some: ['nesting', 'id'],
        },
      },
    ],
    'jsx-a11y/label-has-for': [
      'error',
      {
        required: {
          some: ['nesting', 'id'],
        },
      },
    ],
  },
}
