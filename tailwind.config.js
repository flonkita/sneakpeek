/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    extend: {
      colors: {
        violet: "#8A5DD9",
        noir: "#0B0B0B",
        blanc: "#F8FAFC",
        bleu: "#4167C7",
      },
    },
  },
  plugins: [require("daisyui"), require("@tailwindcss/forms")],
};
