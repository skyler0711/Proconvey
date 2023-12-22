import { useDropzone } from 'react-dropzone'
import Button from '../../Button'
import Label from '../../Form/Label'
import { UploadFileIcon, UploadedFileIcon } from '../../../icons'
import classNames from 'classnames'
import { Transition } from '@headlessui/react'
import Checkbox from '../Checkbox'
import { useEffect, useState } from 'react'

type PropTypes = {
  label?: string
  name?: string
  error?: string
  onChange?: (value: File[] | string) => void
  allowLater?: boolean
  allowNotAvailable?: boolean
  value?: { name: string }[] | string
}

const Upload = ({ label, name, error, onChange, allowLater, allowNotAvailable, value }: PropTypes) => {
  const [checkboxValue, setCheckboxValue] = useState<string | undefined>()

  const { acceptedFiles, getRootProps, getInputProps, isDragReject, isDragAccept } = useDropzone({
    accept: {
      'application/pdf': ['.pdf'],
    },
    onDrop: (acceptedFiles) => {
      setCheckboxValue(undefined)
      onChange?.(acceptedFiles)
    },
  })

  const hasExistingFiles = typeof value !== 'string' && value !== undefined && value.length > 0 && acceptedFiles.length === 0

  const handleCheckboxChange = (value: string) => {
    setCheckboxValue(value)
    onChange?.(value)
  }

  useEffect(() => {
    if (value !== checkboxValue) {
      setCheckboxValue(value as string)
    }
  }, [value, checkboxValue])

  return (
    <>
      {
        label &&
        <Label>{label}</Label>
      }

      <div>
        <div
          {...getRootProps({ className: classNames('cursor-pointer w-full group transition-all flex flex-col gap-5 justify-center items-center border-dashed border-opacity-20 border-2 p-[1.875rem] min-h-[12.375rem] rounded-lg focus:outline-none hover:border-opacity-100 hover:ring-primary-ring  hover:ring-2.5 focus-visible:border-opacity-100 focus-visible:ring-primary-ring focus-visible:ring-2.5', {

            'border-opacity-100 ring-primary-ring ring-2.5': isDragAccept,
            'ring-opacity-50 ring-danger ring-2.5': isDragReject,

            'bg-blue-chalk border-primary': !error,
            'bg-danger/20 border-danger': error,

          }) })}
        >
          <input
            type="file"
            className="hidden"
            name={name}
            {...getInputProps()}
          />
          <div className={classNames('flex items-center', { 'gap-[1.0625rem]': hasExistingFiles || acceptedFiles.length > 0 })}>
            {
              (hasExistingFiles || acceptedFiles.length > 0) ?
                <>
                  <UploadedFileIcon
                    className={classNames('w-[1.875rem] h-[2.0625rem] flex-shrink-0', {
                      'text-primary': !error,
                      'text-danger': error,
                    })}
                  />
                  <div className="flex flex-col flex-wrap gap-1">
                    {
                      ((hasExistingFiles ? value : null) ?? acceptedFiles).map((file) => (
                        <p
                          key={file.name}
                          className={classNames('text-base leading-[1.1875rem]', {
                            'text-body': !error,
                            'text-danger': error,
                          })}
                        >
                          {file.name}
                        </p>
                      ))
                    }
                  </div>

                </>
                :
                <UploadFileIcon
                  className={classNames('w-[1.875rem] h-[2.0625rem] flex-shrink-0', {
                    'text-primary': !error,
                    'text-danger': error,
                  })}
                />
            }
          </div>
          {
            !hasExistingFiles && acceptedFiles.length === 0 &&
            <p
              className={classNames('text-base font-bold leading-5', {
                'text-primary': !error,
                'text-danger': error,
              })}
            >
              Drop file here to upload or choose from your computer
            </p>
          }
          <Button variant={error ? 'danger' : 'primary'} groupFocus={true} size="small" tabIndex={-1}>
            {acceptedFiles.length === 0 ? 'Choose file' : 'Change file'}
          </Button>
        </div>

        {
          (allowLater || allowNotAvailable) &&
          <div className="flex mt-[1.185rem] gap-[1.75rem]">
            {
              allowLater &&
              <Checkbox
                value="1"
                rounded
                size="small"
                selected={checkboxValue === 'Add later' ? ['1'] : []}
                onChange={() => handleCheckboxChange('Add later')}
              >
                Add later
              </Checkbox>
            }

            {
              allowNotAvailable &&
              <Checkbox
                value="1"
                rounded
                size="small"
                selected={checkboxValue === 'Not applicable' ? ['1'] : []}
                onChange={() => handleCheckboxChange('Not applicable')}
              >
                Not applicable
              </Checkbox>
            }
          </div>
        }

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
    </>
  )
}

export default Upload
