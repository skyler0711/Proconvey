import { ComponentStory, ComponentMeta } from '@storybook/react'
import HtmlEditor from './index'

export default {
  title: 'Components/HtmlEditor',
  component: HtmlEditor,
} as ComponentMeta<typeof HtmlEditor>

export const Editor: ComponentStory<typeof HtmlEditor> = () => {
  return <HtmlEditor onChange={console.log} />
}
