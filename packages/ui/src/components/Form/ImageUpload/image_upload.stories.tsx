import { ComponentStory, ComponentMeta } from '@storybook/react'
import { PhotoUploadIcon } from '../../../icons'
import ImageUpload from './index'

export default {
  title: 'Components/Forms/ImageUpload',
  component: ImageUpload,
} as ComponentMeta<typeof ImageUpload>

const Template: ComponentStory<typeof ImageUpload> = (args) => <ImageUpload {...args} />

export const ImageUploadExample: ComponentStory<typeof ImageUpload> = Template.bind({})
ImageUploadExample.args = {
  placeholder: <div className="flex items-center justify-center w-20 h-20"><PhotoUploadIcon className="w-10 h-10" /></div>,
}

export const ErrorImageUpload: ComponentStory<typeof ImageUpload> = Template.bind({})
ErrorImageUpload.args = {
  placeholder: <div className="flex items-center justify-center w-20 h-20"><PhotoUploadIcon className="w-10 h-10" /></div>,
  error: 'Invalid image selected',
}
