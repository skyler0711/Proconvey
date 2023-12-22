import { Listbox, Transition } from '@headlessui/react'
import classNames from 'classnames'
import { CheckmarkIcon, ChevronDownIcon, CrossIcon } from '../../icons'
import { ReactNode, useEffect, useState } from 'react'
import Label from '../../components/Form/Label'

export type MultiSelectItem = {
  id: string
  text: string
}

type PropTypes = {
  placeholder: ReactNode
  positionClassName?: string
  showSelection?: boolean
  onChange?: (ids: string[]) => void
  onOpen?: () => void
  label?: string
  error?: string
  options?: MultiSelectItem[]
  disabled?: boolean
}

const MultiSelect = ({
  placeholder,
  positionClassName = 'left-0',
  options,
  label,
  disabled = false,
  error,
  onChange,
  onOpen,
}: PropTypes) => {
  const [selectedItems, setSelectedItems] = useState<MultiSelectItem[]>([])

  const addItem = (item: any) => {
    if (selectedItems.map((i) => i.id).includes(item.id)) {
      if (selectedItems.length > 0) {
        const newSelectedItems = [...selectedItems]
        newSelectedItems.splice(newSelectedItems.indexOf(item), 1)
        setSelectedItems(newSelectedItems)
      }
    } else {
      setSelectedItems([...selectedItems, item])
    }
  }

  // Return selected item IDs
  useEffect(() => {
    if (onChange) {
      onChange(selectedItems.map((item) => item.id))
    }
  }, [selectedItems, onChange])

  return (
    <div className="flex-1 relative">
      {
        label &&
        <Label>{label}</Label>
      }
      <Listbox value={selectedItems} onChange={addItem}>
        {({ open }) => (
          <>
            <Listbox.Button disabled={disabled} onClick={onOpen} className={`'bg-white text-base placeholder-input-placeholder flex justify-between items-center focus:ring-transparent leading-[1.375rem] border focus:border-input-active focus:outline-none disabled:bg-white text-body text-left w-full max-w-full pr-6 pl-3 py-[14px] rounded-md' ${selectedItems.length > 0 ? 'text-body text-sm' : ''} ${open ? 'rounded-t-lg' : 'rounded-lg focus-visible:ring-2.5 focus-visible:ring-select-ring'} ${!options && ' text-red-400 pointer-events-none'} ${error ? 'border-danger' : 'border-input'}`}>
              <div className="flex">
                {
                  selectedItems
                    ? selectedItems.length > 0
                      ? selectedItems.map((item, index) =>
                        <>
                          <span key={index} className="rounded-full bg-success w-2 h-2 ml-[6px] flex my-auto"></span>
                          <span key={index} className="border px-2 gap-y-2 ml-2 rounded-md self-center">
                            {item.text} <button onClick={() => addItem(item)}><CrossIcon className="h-2.5 w-2.5 hover:text-black scale-75 hover:scale-100 transition-all" /></button>
                          </span>
                        </>,
                      )
                      : placeholder
                    : placeholder
                }
              </div>
              <ChevronDownIcon
                className={`transition h-5 w-5 text-primary ${open ? 'rotate-180' : ''}`}
              />
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

            <Listbox.Options className={`relative -mt-[0.0625rem] border border-input-active border-t-input-ring border-b-input-ring last:border-b-input-active last:rounded-b-lg ${positionClassName} z-10 max-h-80 w-full overflow-y-auto overflow-x-visible rounded-md text-base shadow-lg focus:outline-none sm:text-sm border border-divider`}>
              {
                options && options.map((item) => (
                  <Listbox.Option
                    key={item.id}
                    value={item}
                    className={({ active }) =>
                      classNames(
                        active ? 'text-black bg-secondary-active' : 'bg-white',
                        'relative cursor-pointer select-none px-3 py-4',
                      )
                    }
                  >
                    {({ selected, active }) => (
                      <>
                        <span className={classNames(selected ? 'font-semibold' : 'font-normal', 'block truncate')}>
                          {item.text}
                        </span>
                        {
                          selectedItems.map((item) => item.id).includes(item.id) ? (
                            <span
                              className={classNames(
                                active ? 'text-black' : 'text-primary',
                                'absolute inset-y-0 right-0 flex items-center pl-4 pr-6',
                              )}
                            >
                              <CheckmarkIcon className="h-5 w-5 text-primary" aria-hidden="true" />
                            </span>
                          ) : (
                            <></>
                          )
                        }
                      </>
                    )}
                  </Listbox.Option>
                ))
              }
            </Listbox.Options>
          </>
        )}

      </Listbox>
    </div>
  )
}

export default MultiSelect
