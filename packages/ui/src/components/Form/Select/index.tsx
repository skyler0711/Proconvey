import { useState, useEffect } from 'react'
import { Combobox, Transition } from '@headlessui/react'
import Label from '../Label'
import { ChevronDownIcon } from '../../../icons'
import classNames from 'classnames'

export type SelectOption = {
  value: string
  text: string
  disabled?: boolean
}

type PropTypes = {
  className?: string
  label?: string
  onChange?: (option: SelectOption) => void
  options?: SelectOption[] | []
  defaultValue?: SelectOption
  placeholder?: string
  disabled?: boolean
  error?: string
}

const Select = ({ className, label, onChange, options, defaultValue, placeholder, disabled = false, error }: PropTypes) => {
  const [selectedOption, setSelectedOption] = useState<SelectOption | undefined>(defaultValue)
  const [query, setQuery] = useState<string>('')
  const [hasChanged, setHasChanged] = useState(false)

  useEffect(() => {
    if (defaultValue && !hasChanged) {
      setSelectedOption(defaultValue)
    }
  }, [defaultValue, hasChanged])

  const filteredOptions =
    query === ''
      ? options
      : options && options.filter((option) => {
        return option.text.toLowerCase().includes(query.toLowerCase())
      })

  return (
    <div className={`${disabled ? 'opacity-50' : ''} flex-1 ${className}`}>
      {
        label &&
        <Label>{label}</Label>
      }
      <Combobox
        value={selectedOption}
        disabled={disabled}
        onChange={(option) => {
          setSelectedOption(option)
          setHasChanged(true)

          if (onChange) {
            onChange(option)
          }
        }
        }>
        {({ open }) => (
          <div className="relative">
            <Combobox.Input
              className={classNames(
                'transition text-base text-body placeholder-input-placeholder w-full pr-10 pl-3 py-[14px] focus:ring-transparent leading-[1.375rem] border focus:border-input-active focus:outline-none disabled:bg-white',
                {
                  'rounded-t-lg': open,
                  'rounded-lg focus-visible:ring-2.5 focus-visible:ring-select-ring': !open,

                  'rounded-lg': filteredOptions && filteredOptions.length === 0,

                  'border-danger': error,
                  'border-input': !error,
                },
              )}
              displayValue={(option: SelectOption) => option?.text}
              placeholder={placeholder}
              onChange={(event) => setQuery(event.target.value)}
              autoComplete="off"
            />

            <Transition
              show={Boolean(error)}
              enter="transition"
              enterFrom="opacity-0 -translate-y-1"
              enterTo="opacity-100 translate-y-0"
              leave="transition"
              leaveFrom="opacity-100 translate-0"
              leaveTo="opacity-0 -translate-y-1"
            >
              <div className="text-danger text-[0.875rem] absolute">
                {error}
              </div>
            </Transition>

            {
              (options && options.length > 0) &&
              <Combobox.Button className="absolute inset-y-0 right-0 flex items-center mr-[1.5625rem]">
                <ChevronDownIcon
                  className={`transition h-5 w-5 text-primary ${open ? 'rotate-180' : ''}`}
                />
              </Combobox.Button>
            }

            <Combobox.Options className="absolute z-50 w-full overflow-auto text-base bg-white cursor-pointer max-h-60 focus:outline-none">
              {
                filteredOptions && filteredOptions.map((option: SelectOption) => {
                  return (
                    <Combobox.Option
                      className={({ active, disabled }) => classNames('relative -mt-[0.0625rem] border border-input-active border-t-input-ring border-b-input-ring last:border-b-input-active px-3 py-4 last:rounded-b-lg', {
                        'bg-blue-chalk': active,
                        'bg-white': !active,
                        'text-body/50 cursor-default': disabled,
                      })}
                      key={option.value}
                      value={option}
                      disabled={option.disabled || false}
                    >
                      {option.text}
                    </Combobox.Option>
                  )
                })
              }
            </Combobox.Options>
          </div>
        )
        }
      </Combobox>
    </div>
  )
}

export default Select
