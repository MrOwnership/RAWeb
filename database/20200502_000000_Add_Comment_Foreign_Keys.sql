ALTER TABLE `Comment`
  ADD FOREIGN KEY (`ArticleType`) REFERENCES `ArticleTypeDimension` (`ArticleTypeID`),
  ADD FOREIGN KEY (`UserID`) REFERENCES `UserAccounts` (`ID`)
;
