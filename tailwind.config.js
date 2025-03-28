/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {colors: {
        "transparent": "transparent",
        "base": {
          "light": "#D1E9ED",
          // "light": "#ffecd1",
          "dark": "#001524",
        },
        "surface": {
          "light": "#E8F4F6",
          // "light": "#F8F9FA",
          "dark": "#15616d",
        },
        "content": {
          "primary": {
            "light": "#001524",
            "dark": "#E6E8FF",
          },
          "secondary": "#ff7d00",
        },
        "accent": {
          "light": "#ff7d00",
          "dark": "#ff8d1c",
        },
      }
    },
  },
  plugins: [
    // require('flowbite/plugin')
  ],
}
