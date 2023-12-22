import Button from '../../Button'
import Image from 'next/image'
import { ReactElement, useEffect, useRef, useState } from 'react'
import classNames from 'classnames'

type PropTypes = {
  onChange?: (files: File[] | FileList | null) => void
  onRemove?: () => void;
  error?: string
  placeholder: ReactElement
  type: 'logo_image' | 'profile_image'
  defaultPreviewUrl?: string
}

const ImageUpload = ({ onChange, onRemove, error, placeholder, defaultPreviewUrl, type }: PropTypes) => {
  const [previewUrl, setPreviewUrl] = useState<string | undefined>(defaultPreviewUrl)
  const [defaultURL, setDefaultURL] = useState<string | undefined>(undefined)
  const profilePictureRef = useRef<HTMLInputElement>(null)


  useEffect(() => {
    if (defaultPreviewUrl) {
      setDefaultURL(defaultPreviewUrl)
    }
  }, [defaultPreviewUrl])

  useEffect(() => {
    if (previewUrl) {
      setDefaultURL(previewUrl)
    }
  }, [previewUrl])

  const handleChange = (files: File[] | FileList | null) => {
    if (files) {
      setPreviewUrl(URL.createObjectURL(files[0]))
    }
    onChange?.(files)
  }

  const removeHandler = () => {
    if (profilePictureRef.current) {
      profilePictureRef.current.value = ''
    }
    setPreviewUrl(undefined)
    setDefaultURL(undefined)
    onChange?.(null)
  }

  return (
    <div>
      <div className="flex items-center gap-5">
        <div
          className={classNames('max-w-[8.75rem] flex items-center justify-center border rounded-xl border-primary border-opacity-20', {
            'border-primary': !error,
            'border-danger': error,
          })}
        >
          {previewUrl === undefined && defaultPreviewUrl === undefined ? (
            placeholder
          ) : defaultURL ? (
            <Image src={defaultURL} alt="Profile Picture" width={80} height={80} className="w-full h-full rounded-xl" />
          ) : previewUrl ? (
            <Image src={previewUrl} alt="Profile Picture" width={80} height={80} className="w-full h-full rounded-xl" />
          ) : (
            placeholder
          )}
        </div>


        <div className="text-body text-opacity-60">
          <ul className="flex flex-col gap-5 sm:flex-row">
            {
              type === 'profile_image'
                ? <>
                  <li>Dimensions: Square</li>
                  <li>Format: JPEG, PNG</li>
                  <li>Max size: 5MB</li>
                </>
                : <>
                  <li>Max size: 5MB</li>
                  <li>Format: JPEG, PNG</li>
                </>
            }
          </ul>
          <div className="flex flex-col gap-5 sm:flex-row mt-4">
            <Button
              size="small"
              onClick={() => profilePictureRef.current?.click()}
            >
              Upload Photo
            </Button>
            {
              (previewUrl || defaultURL)  && (
                <Button variant="secondary" onClick={removeHandler}>Remove</Button>
              )
            }
          </div>
        </div>

        <input
          type="file"
          accept="image/jpeg, image/jpg, image/png"
          className="hidden"
          ref={profilePictureRef}
          onChange={(input) => handleChange(input.target.files)}
        />
      </div>

      {error && <div className="text-danger text-[0.875rem]">{error}</div>}
    </div>
  )
}

export default ImageUpload
