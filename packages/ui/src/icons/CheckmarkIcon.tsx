type Proptypes = {
  className?: string,
  ariaHidden?: boolean
}

const CheckmarkIcon = ({ className, ariaHidden = true }: Proptypes) => {
  return (
    <svg width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg" className={className} aria-hidden={ariaHidden}>
      <path fillRule="evenodd" clipRule="evenodd" d="M9.70473 0.507149C9.9462 0.742957 9.95078 1.12986 9.71498 1.37133L3.7471 7.48244C3.62684 7.60558 3.46024 7.6723 3.28823 7.6662C3.11623 7.6601 2.95477 7.58176 2.84353 7.45041L0.25586 4.39486C0.0377415 4.1373 0.0697126 3.75169 0.327269 3.53357C0.584826 3.31545 0.970437 3.34742 1.18856 3.60498L3.34205 6.14785L8.84055 0.517396C9.07636 0.27593 9.46326 0.271342 9.70473 0.507149Z" fill="currentColor" />
    </svg>
  )
}

export default CheckmarkIcon
