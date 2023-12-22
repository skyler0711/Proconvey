import { Children, cloneElement } from 'react'
import ProgressBarItem from './ProgressBarItem'

type PropTypes = {
  children: React.ReactNode
}

const ProgressBar = ({ children }: PropTypes) => {
  const items = Children.toArray(children)

  return (
    <div className="flex overflow-x-auto overflow-y-hidden -mx-5 px-5 pb-5">
      {
        // @ts-ignore
        Children.map(items, (child, index) => (
          <>
            {
              index !== 0 &&
              <div className="rounded-full relative mt-[2.1875rem] h-1.5 sm:min-w-[63px] min-w-[30px] max-w-[130px] w-full mx-1 bg-gainsboro"></div>
            }
            {
              // @ts-ignore
              cloneElement(child)
            }
          </>
        ))
      }
    </div>
  )
}

ProgressBar.Item = ProgressBarItem

export default ProgressBar
