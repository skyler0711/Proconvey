import { Switch as Toggle, Transition } from '@headlessui/react'
import classNames from 'classnames'


type PropTypes = {
  onChange?: (checked: boolean) => void
  value: boolean
  disabled?: boolean
  error?: string
}

const Switch = ({ onChange, value, disabled, error }: PropTypes) => {

  const labelClassNames = classNames('flex items-center cursor-pointer ', {
    'pointer-events-none': disabled,
  })

  const toggleClassNames = classNames('ml-[20px] relative inline-flex h-[1.875rem] w-[3.75rem] items-center rounded-full bg-body/10 ', {
    'bg-mint/10': !value,
  })

  const spanClassNames = classNames('inline-block h-[1.375rem] w-[1.375rem] transform rounded-full transition' , {
    'bg-body/40 translate-x-[0.25rem]': !value,
    'translate-x-[2.125rem] bg-mint': value,
  })

  return (
    <div className="flex flex-col items-end float-right">
      <label
        className={labelClassNames}>
        {value ? 'On' : 'Off'}
        <Toggle
          checked={value}
          onChange={onChange}
          className={toggleClassNames}
        >
          <span className="sr-only">Switch {value ? 'off' : 'on'}</span>
          <span
            className={spanClassNames}
          />
        </Toggle>
      </label>

      <Transition
        show={Boolean(error)}
        enter="transition"
        enterFrom="opacity-0 -translate-y-1"
        enterTo="opacity-100 translate-y-0"
        leave="transition"
        leaveFrom="opacity-100 translate-0"
        leaveTo="opacity-0 -translate-y-1"
      >
        <div className="text-danger text-[0.875rem]">
          {error}
        </div>
      </Transition>
    </div>
  )
}

export default Switch
