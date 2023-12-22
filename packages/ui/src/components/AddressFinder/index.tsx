import Alert from '../Alert'
import { useEffect, useMemo, useRef, useState } from 'react'
import Form from '../Form'
import { FieldError } from 'react-hook-form'

export type Address = {
  line_1: string
  line_2?: string | null
  city: string
  postcode: string
}

type PropTypes = {
  label?: string
  onChange?: (address: Address) => void
  address?: Address
  error?: {
    line_1?: FieldError
    line_2?: FieldError
    city?: FieldError
    postcode?: FieldError
  }
}

const AddressFinder = ({ label, onChange, address, error }: PropTypes) => {
  const line1Ref = useRef<HTMLInputElement>(null)
  const line2Ref = useRef<HTMLInputElement>(null)
  const cityRef = useRef<HTMLInputElement>(null)
  const postcodeRef = useRef<HTMLInputElement>(null)

  const [selectedAddress, setSelectedAddress] = useState<Address>({
    line_1: '',
    line_2: '',
    city: '',
    postcode: '',
  })

  useMemo(() => {
    if (address) {
      setSelectedAddress(address)
      if (line1Ref.current !== null) line1Ref.current!.value = address.line_1
      if (line2Ref.current !== null) line2Ref.current!.value = address.line_2 ?? ''
      if (cityRef.current !== null) cityRef.current!.value = address.city
      if (postcodeRef.current !== null) postcodeRef.current!.value = address.postcode
    }
  }, [address])

  const [generalError, setGeneralError] = useState<string | undefined>()

  const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = event.target

    let newSelectedAddress = { ...selectedAddress, [name]: value }
    setSelectedAddress(newSelectedAddress)
    if (onChange) {
      onChange(newSelectedAddress)
    }
  }

  useEffect(() => {
    if (process.env.NEXT_PUBLIC_ADDRESS_NOW_KEY) {
      const script = document.createElement('script')
      script.src = `https://api.addressnow.co.uk/js/addressnow-2.20.min.js?key=${process.env.NEXT_PUBLIC_ADDRESS_NOW_KEY}`
      script.async = true
      document.body.appendChild(script)
    }

    let counter = 0

    const timer = setInterval(() => {
      const w = window as any
      if (w.addressNow) {
        clearInterval(timer)

        w.addressNow.listen('error', () => {
          setGeneralError('There was an error searching for addresses, please enter your address manually')
        })

        w.addressNow.listen('populate', (_: any) => {
          const newAddress = {
            line_1: line1Ref.current?.value ?? '',
            line_2: line2Ref.current?.value ?? '',
            city: cityRef.current?.value ?? '',
            postcode: postcodeRef.current?.value ?? '',
          }
          setSelectedAddress(newAddress)
          onChange?.(newAddress)
        })
      } else {
        counter++
        if (counter > 100) {
          clearInterval(timer)
          setGeneralError('There was an error loading the address search, please enter your address manually')
        }
      }
    }, 50)
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <link rel="stylesheet" href={`https://api.addressnow.co.uk/css/addressnow-2.20.min.css?key=${process.env.NEXT_PUBLIC_ADDRESS_NOW_KEY}`} />

      <Form.Input
        label={label}
        name="address"
        placeholder="Start searching for your address"
        defaultValue={[
          address?.line_1, address?.line_2, address?.city, address?.postcode,
        ].filter(Boolean).join(', ')}
      />

      {
        generalError &&
        <Alert className="mt-[0.5rem]" variant="danger">{generalError}</Alert>
      }

      <div className="mt-[15px]">
        <Form as="div">
          <Form.Group>
            <Form.Input
              placeholder="Address Line 1"
              name="line_1"
              defaultValue={address?.line_1}
              onChange={handleChange}
              ref={line1Ref}
              error={error?.line_1?.message}
            />

            <Form.Input
              placeholder="Address Line 2 (Optional)"
              name="line_2"
              defaultValue={address?.line_2 ?? ''}
              onChange={handleChange}
              ref={line2Ref}
              error={error?.line_2?.message}
            />
          </Form.Group>

          <Form.Group>
            <Form.Input
              placeholder="Town/City"
              name="city"
              defaultValue={address?.city}
              onChange={handleChange}
              ref={cityRef}
              error={error?.city?.message}
            />

            <Form.Input
              placeholder="Postcode"
              name="postcode"
              defaultValue={address?.postcode}
              onChange={handleChange}
              ref={postcodeRef}
              error={error?.postcode?.message}
            />
          </Form.Group>
        </Form>
      </div>
    </>
  )
}

export default AddressFinder
