type Proptypes = {
    className?: string,
  }

const TAFormIcon = ({ className = 'w-6 h-6' }: Proptypes) => {
  return (
    <svg width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
      <rect y="0.5" width="40" height="40" rx="10" fill="#674186" fillOpacity="0.1" />
      <path d="M25.5 19H20.5M25.5 23H20.5M25.5 15H20.5M17 11L17 29M15.8 11H24.2C25.8802 11 26.7202 11 27.362 11.327C27.9265 11.6146 28.3854 12.0735 28.673 12.638C29 13.2798 29 14.1198 29 15.8V24.2C29 25.8802 29 26.7202 28.673 27.362C28.3854 27.9265 27.9265 28.3854 27.362 28.673C26.7202 29 25.8802 29 24.2 29H15.8C14.1198 29 13.2798 29 12.638 28.673C12.0735 28.3854 11.6146 27.9265 11.327 27.362C11 26.7202 11 25.8802 11 24.2V15.8C11 14.1198 11 13.2798 11.327 12.638C11.6146 12.0735 12.0735 11.6146 12.638 11.327C13.2798 11 14.1198 11 15.8 11Z" stroke="#674186" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>

  )
}

export default TAFormIcon
