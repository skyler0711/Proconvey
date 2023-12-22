import classNames from 'classnames'
import { RadioGroup } from '@headlessui/react'
import { CheckmarkIcon } from '../../../icons'
import { ReactNode } from 'react'

type PropTypes = {
  children: ReactNode
  value: string | boolean | number
  className?: string,
  error?: string
}

const Checkmark = ({ checked = false }: { checked: boolean }) => {
  const circleClassName = classNames('z-10 rounded-full min-w-[1.375rem] min-h-[1.375rem] flex justify-center items-center border border-[0.125rem] ', {
    'bg-mint border-mint': checked,
  })

  const checkMarkClassName = classNames({
    'text-gainsboro': !checked,
    'text-white': checked,
  })

  return (
    <div className={circleClassName}>
      <CheckmarkIcon className={checkMarkClassName} />
    </div>
  )
}

const Radio = ({
  value,
  children,
  className,
  error,
}: PropTypes) => {
  return (
    <RadioGroup.Option
      value={value}
      className={({ checked }) => classNames('flex items-center cursor-pointer min-w-[260px] gap-3.5 bg-white border border-opacity-[15%] rounded-lg py-[0.875rem] px-5 text-base text-body leading-[1.4375rem] font-medium focus:ring-2.5 focus:ring-secondary-ring hover:ring-2.5 hover:ring-secondary-ring transition-all ease-in-out',
        {
          'border-opacity-100 ring-primary': checked && !error,
          'ring-[0.0625rem]': checked,
          'border-danger border-opacity-100 ring-danger': error,
          'border-primary': !error,
        },
        className,
      )}
    >
      {({ checked }: { checked: boolean }) => (
        <>
          <Checkmark checked={checked} />
          {children}
        </>
      )}
    </RadioGroup.Option>
  )
}

export default Radio
