import { CardCvcElement } from '@stripe/react-stripe-js'

type PropTypes = {
  error? : string
  onChange?: (event: any) => void
}

const CardCvc = ({ error, onChange }: PropTypes) => {

  const handleChange = (event: any) => {
    if (onChange) {
      onChange(event)
    }
  }

  let errorClass = !error ? '' : 'border !border-danger'

  return (
    <>
      <CardCvcElement
        options={{
          placeholder: 'CVV',
          classes: {
            base: 'transition font-sans text-base text-body placeholder-input-placeholder w-full px-3 py-[14px] leading-[1.375rem] rounded-lg border border-input',
            focus: 'border-input-active outline-none ring-2.5 ring-input-ring',
            empty: errorClass,
          },
        }}
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

export default CardCvc
