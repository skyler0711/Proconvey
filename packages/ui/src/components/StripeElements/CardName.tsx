type PropTypes = {
    error?: string,
    onChange?: (event: any) => void
}


const CardName = ({ error, onChange } : PropTypes) => {
  const handleChange = (event: any) => {
    if (onChange) {
      onChange(event)
    }
  }

  let errorClass = !error ? '' : 'border !border-danger'

  return (
    <>
      <input
        type="text"
        className={`transition font-sans text-base text-body placeholder-input-placeholder w-full px-3 py-[14px] leading-[1.375rem] rounded-lg border border-input ${errorClass}`}
        placeholder="e.g. John Doe"
        onChange={handleChange}
      />
      {error &&
                <div className="text-danger text-[0.875rem]">
                  {error}
                </div>
      }
    </>
  )
}

export default CardName
