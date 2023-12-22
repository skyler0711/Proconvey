type Proptypes = {
  className?: string
}

const UnCheckedIconSVG = ({ className }: Proptypes) => {
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="12" r="11" fill="white" stroke="#E3E3E3" strokeWidth="2" />
      <path fillRule="evenodd" clipRule="evenodd" d="M17.1325 8.18971C17.396 8.44695 17.401 8.86903 17.1437 9.13245L10.6333 15.7991C10.5021 15.9334 10.3204 16.0062 10.1327 15.9996C9.94508 15.9929 9.76895 15.9075 9.6476 15.7642L6.82469 12.4308C6.58674 12.1499 6.62162 11.7292 6.90259 11.4913C7.18356 11.2533 7.60422 11.2882 7.84217 11.5692L10.1914 14.3432L16.1898 8.20088C16.447 7.93747 16.8691 7.93246 17.1325 8.18971Z" fill="#E3E3E3" />
    </svg>
  )
}

export default UnCheckedIconSVG
