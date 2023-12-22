import { useState, useEffect } from 'react'
import { Listbox, Transition } from '@headlessui/react'
import Label from '../Label'
import { CheckmarkIcon, ChevronDownIcon } from '../../../icons'
import classNames from 'classnames'

export type MultipleSelectOption = {
  value: string
  text: string
  disabled?: boolean
}

type PropTypes = {
  className?: string
  label?: string
  onChange?: (option: MultipleSelectOption[]) => void
  options?: MultipleSelectOption[] | []
  defaultValue?: MultipleSelectOption[] | []
  placeholder?: string
  disabled?: boolean
  error?: string
}

const MultipleSelect = ({ className, label, onChange, options, defaultValue, placeholder, disabled = false, error }: PropTypes) => {
  const [selectedItems, setSelectedItems] = useState<MultipleSelectOption[]>([])
  const [hasChanged, setHasChanged] = useState(false)

  useEffect(() => {
    if (defaultValue && !hasChanged && (selectedItems.length === 0 || ! selectedItems.every((item, index) => item.value === defaultValue[index].value))) {
      setSelectedItems(defaultValue)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [defaultValue, hasChanged])

  const addItem = (item: any) => {
    if (selectedItems.map((i) => i.value).includes(item.value)) {
      if (selectedItems.length > 0) {
        const newSelectedItems = [...selectedItems]
        newSelectedItems.splice(
          selectedItems.map(item => item.value).indexOf(item.value),
          1,
        )
        setSelectedItems(newSelectedItems)
      }
    } else {
      setSelectedItems([...selectedItems, item])
    }
    setHasChanged(true)
  }

  useEffect(() => {
    let isDefaultItems = defaultValue
      ? selectedItems.every((item, index) => item.value === defaultValue[index]?.value)
      : false

    if (onChange && !isDefaultItems) {
      onChange(selectedItems)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedItems])


  return (
    <div className={`${disabled ? 'opacity-50' : ''} flex-1 ${className}`}>
      {
        label &&
        <Label>{label}</Label>
      }
      <Listbox
        value={selectedItems}
        onChange={addItem}
      >
        {({ open }) => (
          <div className="relative">
            <Listbox.Button
              className={classNames(
                'relative transition text-base w-full pr-10 pl-3 py-[14px] focus:ring-transparent leading-[1.375rem] border focus:border-input-active focus:outline-none disabled:bg-white',
                {
                  'rounded-t-lg': open,
                  'rounded-lg focus-visible:ring-2.5 focus-visible:ring-select-ring': !open,

                  'text-body': selectedItems.length > 0,
                  'text-input-placeholder': selectedItems.length === 0,

                  'border-danger': error,
                  'border-input': !error,
                },
              )}
              placeholder={placeholder}
            >
              {
                selectedItems.length > 0
                  ? selectedItems.length === options?.length
                    ? 'All people'
                    : selectedItems.filter(i => !!i).map(option => option?.text).join(', ')
                  : placeholder
              }

              <div className="absolute inset-y-0 right-0 flex items-center mr-[1.5625rem]">
                <ChevronDownIcon
                  className={`transition h-5 w-5 text-primary ${open ? 'rotate-180' : ''}`}
                />
              </div>
            </Listbox.Button>

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

            <Listbox.Options className="absolute z-50 w-full overflow-auto text-base bg-white cursor-pointer max-h-60 focus:outline-none">
              {
                options && options.map((option: MultipleSelectOption) => {
                  return (
                    <Listbox.Option
                      className={({ active, disabled }) => classNames('relative -mt-[0.0625rem] border border-input-active border-t-input-ring border-b-input-ring last:border-b-input-active px-3 py-4 last:rounded-b-lg flex justify-between items-center', {
                        'bg-blue-chalk': active,
                        'bg-white': !active,
                        'text-body/50 cursor-default': disabled,
                      })}
                      key={option.value}
                      value={option}
                      disabled={option.disabled || false}
                    >
                      {option.text}
                      {selectedItems.some(item => item.value === option.value) && <CheckmarkIcon className="w-3 h-3 mr-4 text-primary" />}
                    </Listbox.Option>
                  )
                })
              }
            </Listbox.Options>
          </div>
        )
        }
      </Listbox>
    </div>
  )
}

export default MultipleSelect
