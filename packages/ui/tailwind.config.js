const defaultTheme = require('tailwindcss/defaultTheme')

const daisy = '#674186'
const body = '#3D403D'
const mull = '#BF4A8E'
const peach = '#F3B790'
const alert = '#FDF7F3'
const danger = '#E21219'
const chalk = '#E9E3EE'
const blueChalk = '#F7F5F9'
const chalkie = '#FCF6F9'
const gainsboro = '#E3E3E3'
const selago = '#F6F4F8' // EFECF2
const snuff = '#E9E4EE' //E8E3ED
const mint = '#62C0C1'
const crystalBlue = '#4DB4B5'
const titan = '#E8E4ED'
const oceano = '#98D5E5'
const sentimentalPink = '#F9EDF4'
const success = '#17B26A'
const navy = '#003366'

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.{js,jsx,ts,tsx,css}',
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['GT Eesti Pro', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        'body': body,

        'primary': daisy,
        'primary-active': '#4C266B',
        'primary-hover': '#85679E',
        'primary-focus': '#664185',
        'primary-disabled': '#E1D9E7',
        'primary-ring': snuff,

        'secondary': selago,
        'secondary-active': snuff,
        'secondary-hover': blueChalk,
        'secondary-focus': selago,
        'secondary-disabled': '#F6F5F8',
        'secondary-ring': snuff,

        'outlined': blueChalk,
        'outlined-active': selago,
        'outlined-hover': '#FFFFFF',
        'outlined-focus': blueChalk,
        'outlined-ring': snuff,

        'input': '#E9E4Ef',
        'input-placeholder': '#8B8C8C',
        'input-active': '#BCABCB',
        'input-ring': snuff,
        'input-disabled': '#F4F2F7',

        'mull': mull,
        'peach': peach,
        'alert': alert,
        'danger': danger,
        'chalk': chalk,
        'blue-chalk': blueChalk,
        'gainsboro': gainsboro,
        'selago': selago,
        'snuff': snuff,
        'mint': mint,
        'chalkie': chalkie,
        'sentimental-pink': sentimentalPink,
        'crystal-blue': crystalBlue,
        'oceano': oceano,
        'success': success,
        'navy': navy,

        'select-ring': titan,
      },
      ringWidth: {
        '2.5': '6px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
