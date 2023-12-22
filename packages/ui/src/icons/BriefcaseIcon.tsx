type Proptypes = {
  className?: string,
  ariaHidden?: boolean
}

const BriefcaseIcon = ({ className = 'h-6 w-6', ariaHidden = true }: Proptypes) => {
  return (
    <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" className={className} aria-hidden={ariaHidden}>
      <path fillRule="evenodd" clipRule="evenodd" d="M6 6H4H2L2 8L8.99362 8.00001C8.99575 8 8.99787 8 9 8H11C11.0022 8 11.0045 8 11.0067 8.00001L18 8.00001V8V6H16L14 6H12L8 6H6ZM18 10L13 10V11C13 12.1046 12.1046 13 11 13H9C7.89543 13 7 12.1046 7 11V10L2 10L2 14L2 16H4L16 16H18V14V10ZM9 10V11H11V10L9 10ZM14 4L18 4C19.1046 4 20 4.89543 20 6V16C20 17.1046 19.1046 18 18 18L2 18C0.89543 18 0 17.1046 0 16V6C0 4.89543 0.895429 4 2 4H6V3C6 1.34315 7.34315 0 9 0H11C12.6569 0 14 1.34315 14 3V4ZM12 4H11H10L9 4H8V3C8 2.44795 8.44734 2.00038 8.9993 2L9 2L11 2L11.0007 2C11.5527 2.00038 12 2.44795 12 3V4Z" fill="currentColor" />
    </svg>
  )
}

export default BriefcaseIcon
